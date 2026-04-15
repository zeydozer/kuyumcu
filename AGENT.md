# AGENT.md

Bu dosya, bu repo uzerinde calisan ajanlar icin kisa operasyon rehberidir.

## Proje Ozeti

Bu repo, kuyumcu/siparis operasyonu icin hazirlanmis bir Laravel 8 uygulamasidir. Uygulama; kullanici, kategori, urun, sepet ve siparis yonetimini icerir. Web arayuzu Blade ile render edilir, ekran davranislari ise buyuk olcude `public/js` altindaki jQuery dosyalariyla yurur.

## Stack

- PHP `^7.3|^8.0`
- Laravel `^8.75`
- MySQL
- Blade templates
- jQuery
- Bootstrap 5
- Laravel Mix mevcut, fakat aktif ekranlarin ana CSS/JS dosyalari dogrudan `public/` altindan servis edilir

## Onemli Dizinler

- `routes/web.php`: sayfa rotalari, login/logout, session token ve tema modu
- `routes/api.php`: JSON API uclari
- `app/Http/Controllers`: is kurallari ve CRUD akislari
- `app/Models`: Eloquent modelleri, soft delete ve bazi cascade davranislari
- `app/Http/Middleware`: ozel auth middleware'leri
- `resources/views`: Blade ekranlari
- `public/js`: ekran bazli legacy frontend davranislari
- `public/css`: ekran bazli stiller
- `config/const.php`: siparis durumlari, urun tipi olcu araliklari, varsayilan yonlendirme
- `database/seeders/DatabaseSeeder.php`: standart seed yerine zaman zaman veri duzeltme amacli kullanilmis

## Calistirma

Ilk kurulum icin tipik akis:

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
npm install
php artisan serve
```

Frontend build gerekiyorsa:

```powershell
npm run dev
```

Test calistirmak icin:

```powershell
php artisan test
```

## Kritik Gercekler

- Repo icinde migration dosyalari yok. Veritabani semasini repo tek basina yeniden kuramiyor olabilir.
- `README.md` su anda varsayilan Laravel icerigi; proje baglami icin referans alinmamali.
- Bazi dosyalarda karakter kodlama bozulmasi var. Dosyalari topluca yeniden encode etmeyin; sadece hedef satirlari dikkatli duzenleyin.
- Fotograf yuklemeleri dogrudan `public/img/product` ve `public/img/cart` altina yaziliyor.
- Soft delete iliskileri yalnizca foreign key ile degil, model `boot()` event'leriyle de temizleniyor.

## Auth ve Istek Akisi

- API login noktasi `POST /api/login`.
- Basarili login sonrasi token `users.token` alanina yaziliyor.
- Web tarafi bu token'i `POST /token` ile session icine koyuyor.
- API istekleri `Authorization: Bearer <token>` basligi ile yapiliyor.
- Erisim kontrolu ozel middleware'lerle saglaniyor:
  - `auth.api`
  - `auth.web`
  - `auth.admin`
  - `auth.common`

## Domain Notlari

- Ana varliklar: `User`, `Category`, `Product`, `Cart`, `Order`, `Window`
- `Cart` ve `OrderCart` icindeki olcu iliskisi urun tipine gore dinamik cozuluyor.
- Mevcut urun tipi akisi `bracelets` ustune kurulu. Yukseklik/adet kolonlari `height_56` ... `height_74` formatinda tutuluyor.
- Siparis olusturma akisi sepet kayitlarini siparis snapshot'ina kopyaliyor; bu yuzden `OrderController` ve `CartController` birlikte dusunulmeli.
- `Window` modeli, kullanici bazli urun siralama/one cikarma davranisinda kullaniliyor.

## Frontend Konvansiyonlari

- Sayfalar Blade ile render edilir, veri yukleme ve form islemleri jQuery AJAX ile yapilir.
- Ortak pagination/sort/search yardimcilari `public/js/main.js` icindedir.
- Ekran bazli JS/CSS adlari cogunlukla view adiyla eslesir:
  - `resources/views/order/list.blade.php` -> `public/js/order.js`
  - `resources/views/product/list.blade.php` -> `public/js/product.js`
- Mevcut isi cozmek icin gerekmedikce bu katmani React/Vue benzeri baska bir yapiya tasimayin.

## Degisiklik Yaparken

- Once ilgili ekranin uclu eslesmesini bulun: Blade view, ilgili `public/js` dosyasi, ilgili controller/API ucu.
- API response yapisini degistirirken sayfa JS tarafindaki beklentileri kontrol edin.
- Siparis/sepet hesaplarinda hem `quantity` hem `weight_total` alanlarini birlikte dogrulayin.
- Soft delete kullanan modellerde silme sonrasi bagli kayitlarin nasil temizlendigini kontrol edin.
- Yeni urun tipi eklemek isterseniz sadece config degil; model, iliski, controller, view ve JS tarafini da tamamlamaniz gerekir.

## Test ve Dogrulama

- Test kapsamasi su an zayif; `tests/` altinda cogunlukla varsayilan ornek testler var.
- Is kurali degistiriyorsaniz mumkunse en az bir test ekleyin.
- Dokunulan akis UI ile tetikleniyorsa manuel dogrulama adimlarini da not edin.

## Bu Repoda Kacinilacak Seyler

- Gerekmedikce toplu formatlama veya toplu encoding donusumu yapmayin.
- Migration yokken sema hakkinda varsayim yapmayin.
- Legacy jQuery akisini kiracak response alani degisikliklerini sessizce yapmayin.
- `public/img/...` altindaki kullanici verilerini temizleyen veya yeniden adlandiran degisiklikleri dikkatli planlamadan yapmayin.
