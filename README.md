# Kuyumcu

Legacy Laravel 8 uygulamasi. Siparis, urun, sepet, kategori ve kullanici yonetimi icin kullaniliyor.

## Docker Compose

Projeyi Docker ile kaldirmak icin:

```powershell
docker compose up --build -d
```

Servisler:

- Uygulama: `http://localhost:8000`
- MySQL: `localhost:3307`

Ilk acilista:

- `.env` yoksa `.env.docker.example` kopyalanir
- `composer install` otomatik calisir
- `APP_KEY` otomatik uretilir
- `php artisan migrate --force` otomatik calisir
- `php artisan db:seed --force` otomatik calisir

Sistemi kapatmak icin:

```powershell
docker compose down
```

Volume'lerle birlikte sifirdan kurmak icin:

```powershell
docker compose down -v
docker compose up --build -d
```

## Production: Docker Compose icinde Nginx ve Let's Encrypt

`bilezik.zeydozer.com` gibi bir domaini bu makineye yonlendirdiyseniz, artik reverse proxy olarak host degil `docker compose` icindeki `nginx` servisi kullanilir. SSL sertifikasi da compose icindeki `certbot` servisiyle yonetilir.

Hazirlanan dosyalar:

- `docker/nginx/`: custom Nginx image, HTTP/HTTPS template'leri ve otomatik reload mantigi
- `docker-compose.yml`: `nginx` ve `certbot` servisleri
- `.env.production.example`: domain ve Let's Encrypt ayarlari
- `deploy/compose/README.md`: adim adim kurulum

Kisa akis:

```powershell
Copy-Item .env.production.example .env
docker compose up --build -d
docker compose run --rm --entrypoint certbot certbot certonly --webroot -w /var/www/certbot -d bilezik.zeydozer.com -m mail@example.com --agree-tos --no-eff-email --deploy-hook "touch /var/www/certbot/.nginx-reload"
```

Sertifika geldikten sonra `nginx` servisi paylasilan volume'daki degisikligi gorup HTTPS config'ine reload olur.

Alternatif olarak host seviyesinde Nginx kullanmak isterseniz eski notlar halen duruyor:

- Windows host kurulumu: `deploy/windows/README.md`
- Windows Nginx dosyalari: `deploy/windows/nginx/`
- Linux/VPS kurulumu: `deploy/README.md`
- Linux Nginx dosyalari: `deploy/nginx/`
- Production env ornegi: `.env.production.example`

Notlar:

- `docker-compose.yml` icinde `nginx` servisi `80/443` portlarini disari acar.
- Uygulama yine `127.0.0.1:${APP_PORT}` ile lokal debug icin erisilebilir kalir.
- Nginx upstream'i varsayilan olarak `app:80` kullanir.
- Production ortaminda `APP_URL=https://bilezik.zeydozer.com` ve `SESSION_SECURE_COOKIE=true` kullanin.

## Veritabani Notu

Projeye temel migration seti eklendi. Elinizde mevcut bir SQL dump varsa yine de `docker/mysql/init/` altina koyabilirsiniz.

Desteklenen dosyalar:

- `.sql`
- `.sql.gz`
- `.sh`

Bu dosyalar sadece MySQL volume'u ilk olusurken otomatik uygulanir. Sonradan import etmek isterseniz once `docker compose down -v` calistirin.

Varsayilan gelistirme kullanicilari:

- Admin: `admin@example.com` / `password`
- Musteri: `customer@example.com` / `password`

Bu degerleri `.env` veya `.env.docker.example` icindeki `INIT_*` alanlariyla degistirebilirsiniz.

Demo seed kapsami:

- 2 personel
- 8 sabit musteri
- 1000 siparis
- 10 kategori
- 100 urun

## Yararli Komutlar

```powershell
docker compose ps
docker compose logs -f app
docker compose logs -f mysql
docker compose exec app php artisan test
docker compose exec app php artisan tinker
```
