# Production Kurulumu (Docker Compose Icindeki Nginx)

Bu akis, `bilezik.zeydozer.com` alan adinin bu bilgisayarin internetten gorunen IP adresine yonlendirilmis oldugunu varsayar.

## 0. Gerekenler

Let's Encrypt `HTTP-01` dogrulamasi `80` portundan calisir. Bu nedenle:

- Domain'in A kaydi bu baglantinin public IP adresine gitmeli
- Modem/router uzerinde `80` ve `443` portlari bu bilgisayara forward edilmeli
- Windows Firewall veya kullandiginiz firewall uzerinde `80/TCP` ve `443/TCP` acik olmali

## 1. Production env hazirlayin

```powershell
Copy-Item .env.production.example .env
```

`.env` icinde en az su alanlari doldurun:

- `APP_URL=https://bilezik.zeydozer.com`
- `APP_DOMAIN=bilezik.zeydozer.com`
- `LETSENCRYPT_EMAIL=mail@example.com`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `INIT_ADMIN_PASSWORD`
- `INIT_CUSTOMER_PASSWORD`

## 2. Servisleri kaldirin

```powershell
docker compose up --build -d
docker compose ps
```

Beklenen servisler:

- `nginx`: domain icin public entrypoint
- `app`: Laravel uygulamasi
- `mysql`: veritabani
- `certbot`: yenileme dongusu

## 3. Ilk sertifikayi alin

Ilk sertifika icin su komutu calistirin:

```powershell
docker compose run --rm --entrypoint certbot certbot `
  certonly `
  --webroot -w /var/www/certbot `
  -d bilezik.zeydozer.com `
  -m mail@example.com `
  --agree-tos `
  --no-eff-email `
  --deploy-hook "touch /var/www/certbot/.nginx-reload"
```

Bu komut sertifikayi paylasilan volume'a yazar. Ardindan `nginx` servisi en gec `NGINX_RELOAD_POLL_SECONDS` kadar sonra config'i yeniden render edip HTTPS moduna gecer.

## 4. HTTPS'i dogrulayin

Tarayicida:

- `http://bilezik.zeydozer.com`
- `https://bilezik.zeydozer.com`

Iki durumda da uygulama acilmali; HTTP istekleri HTTPS'e yonlenmelidir.

## 5. Yenilemeyi test edin

```powershell
docker compose run --rm --entrypoint certbot certbot `
  renew `
  --dry-run `
  --webroot -w /var/www/certbot `
  --deploy-hook "touch /var/www/certbot/.nginx-reload"
```

## Notlar

- `certbot` servisi arka planda `renew` dongusu calistirir.
- Ilk sertifika alinmadan once `nginx` yalnizca HTTP config'i ile baslar.
- Sertifika geldikten sonra ayni container HTTPS config'ine gecer; ayrica elle restart gerekmez.
- Lokal debug icin uygulama `http://127.0.0.1:8000` adresinde de acik kalir.
- `docker compose up` sirasinda `80` veya `443` portu doluysa, IIS/WAMP/XAMPP/yerel Nginx gibi o portlari kullanan servisi durdurun.
- Loglar icin `docker compose logs -f nginx`, `docker compose logs -f certbot`, `docker compose logs -f app` kullanin.
