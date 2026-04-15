# Production Kurulumu (Linux/VPS)

Bu akis, `bilezik.zeydozer.com` alan adinin sunucu IP'sine yonlendirilmis oldugunu varsayar.

Windows uzerinde ayni bilgisayarda calistiracaksaniz `deploy/windows/README.md` dosyasini kullanin.

## 1. Uygulamayi hazirlayin

Sunucuda repo dizininde:

```bash
cp .env.production.example .env
```

`.env` icinde en az su alanlari duzenleyin:

- `APP_KEY`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `INIT_ADMIN_PASSWORD`
- `INIT_CUSTOMER_PASSWORD`

`APP_URL` degeri `https://bilezik.zeydozer.com` olarak kalmali.

## 2. Docker servislerini kaldirin

```bash
docker compose up --build -d
docker compose ps
```

Uygulama host ustunde yalnizca `127.0.0.1:8000` portuna acilir. Nginx bu porta proxy yapar.

## 3. Host ustune Nginx ve Certbot kurun

Ubuntu/Debian icin:

```bash
sudo apt update
sudo apt install -y nginx certbot
```

Gerekirse firewall'da `80/tcp` ve `443/tcp` acin.

## 4. HTTP site dosyasini etkinlestirin

```bash
sudo mkdir -p /var/www/certbot
sudo cp deploy/nginx/bilezik.zeydozer.com.http.conf /etc/nginx/sites-available/bilezik.zeydozer.com
sudo ln -sf /etc/nginx/sites-available/bilezik.zeydozer.com /etc/nginx/sites-enabled/bilezik.zeydozer.com
sudo nginx -t
sudo systemctl reload nginx
```

Varsayilan Nginx site'i aktifse kapatmak icin:

```bash
sudo rm -f /etc/nginx/sites-enabled/default
sudo nginx -t
sudo systemctl reload nginx
```

## 5. Let's Encrypt sertifikasini alin

`mail@example.com` kismini kendi e-posta adresinizle degistirin:

```bash
sudo certbot certonly --webroot -w /var/www/certbot -d bilezik.zeydozer.com -m mail@example.com --agree-tos --no-eff-email
```

## 6. SSL'li site dosyasina gecin

```bash
sudo cp deploy/nginx/bilezik.zeydozer.com.ssl.conf /etc/nginx/sites-available/bilezik.zeydozer.com
sudo nginx -t
sudo systemctl reload nginx
```

## 7. Yenilemeyi test edin

```bash
sudo certbot renew --dry-run
```

## Notlar

- Nginx upstream'i varsayilan olarak `127.0.0.1:8000` kullanir. `.env` icinde `APP_PORT` degisirse iki Nginx dosyasindaki upstream'i de ayni portla guncelleyin.
- Reverse proxy arkasinda HTTPS algisinin dogru calismasi icin Laravel tarafinda trusted proxy ayari acildi.
- Ilk kurulumdan sonra uygulama loglari icin `docker compose logs -f app`, Nginx loglari icin `/var/log/nginx/error.log` kontrol edilebilir.
