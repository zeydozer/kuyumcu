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
