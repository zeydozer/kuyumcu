# Production Kurulumu (Windows Host)

Bu akis, `bilezik.zeydozer.com` alan adinin bu bilgisayarin internetten gorunen IP adresine yonlendirilmis oldugunu varsayar.

## 0. Gerekenler

Let's Encrypt `http-01` dogrulamasi icin sunucuya internetten `80` portu ile ulasilabilmesi gerekir. Bu nedenle:

- Domain'in A kaydi bu internet baglantisinin public IP'sine gitmeli
- Modem/router uzerinde `80` ve `443` portlari bu bilgisayarin LAN IP'sine forward edilmeli
- Windows Defender Firewall uzerinde inbound `80/TCP` ve `443/TCP` acik olmali

Eger ISS'niz `80` portunu blokluyorsa veya CGNAT arkasindaysaniz, bu akis calismaz. O durumda DNS tabanli bir ACME dogrulamasi gerekir.

## 1. Uygulamayi hazirlayin

Repo dizininde:

```powershell
Copy-Item .env.production.example .env
```

`.env` icinde en az su alanlari duzenleyin:

- `APP_KEY`
- `DB_PASSWORD`
- `MYSQL_ROOT_PASSWORD`
- `INIT_ADMIN_PASSWORD`
- `INIT_CUSTOMER_PASSWORD`

`APP_URL` degeri `https://bilezik.zeydozer.com` olarak kalmali.

## 2. Docker servislerini kaldirin

```powershell
docker compose up --build -d
docker compose ps
```

Uygulama yalnizca `127.0.0.1:8000` portuna acilir. Nginx bu porta proxy yapar.

## 3. Nginx for Windows kurun

Nginx'i `C:\nginx` altina acin. Sonra su klasorleri olusturun:

```powershell
New-Item -ItemType Directory -Force C:\nginx\conf\sites
New-Item -ItemType Directory -Force C:\nginx\acme
New-Item -ItemType Directory -Force C:\nginx\certs\bilezik.zeydozer.com
```

Ana `C:\nginx\conf\nginx.conf` dosyasi icindeki `http {}` bloguna su satiri ekleyin:

```nginx
include conf/sites/*.conf;
```

Ilk sertifika alimindan once HTTP config'i kopyalayin:

```powershell
Copy-Item deploy\windows\nginx\bilezik.zeydozer.com.http.conf C:\nginx\conf\sites\bilezik.zeydozer.com.conf
```

Nginx'i test edip baslatin:

```powershell
cd C:\nginx
.\nginx.exe -t
.\nginx.exe
```

Calisiyorsa `http://bilezik.zeydozer.com` istekleri uygulamaya dusmeli.

## 4. win-acme ile Let's Encrypt sertifikasi alin

Windows icin `win-acme` araci pratik secenektir. `C:\Program Files\win-acme` altina acin ve yonetici olarak `wacs.exe` calistirin.

Interaktif akista su secimleri yapin:

- `M` secin
- Source olarak manual host name secin
- Domain olarak `bilezik.zeydozer.com` girin
- Validation method olarak local path / filesystem secin
- Webroot olarak `C:\nginx\acme` verin
- Store method olarak `.pem files` secin
- PEM path olarak `C:\nginx\certs\bilezik.zeydozer.com` verin
- Installation step olarak script secin
- Script olarak repo icindeki `deploy\windows\reload-nginx.ps1` dosyasinin tam yolunu verin
- Script parametresi olarak `-NginxRoot 'C:\nginx'` verin

Isterseniz ayni isi komut satirindan da yapabilirsiniz:

```powershell
& 'C:\Program Files\win-acme\wacs.exe' `
  --source manual `
  --host bilezik.zeydozer.com `
  --validation filesystem `
  --webroot 'C:\nginx\acme' `
  --store pemfiles `
  --pemfilespath 'C:\nginx\certs\bilezik.zeydozer.com' `
  --installation script `
  --script 'C:\Users\YOUR_USER\Desktop\kuyumcu\deploy\windows\reload-nginx.ps1' `
  --scriptparameters "-NginxRoot 'C:\nginx'"
```

## 5. SSL config'ine gecin

Sertifika dosyalari olustuktan sonra SSL config'i kopyalayin:

```powershell
Copy-Item deploy\windows\nginx\bilezik.zeydozer.com.ssl.conf C:\nginx\conf\sites\bilezik.zeydozer.com.conf
cd C:\nginx
.\nginx.exe -t
.\nginx.exe -s reload
```

Beklenen PEM dosyalari:

- `C:\nginx\certs\bilezik.zeydozer.com\bilezik.zeydozer.com-chain.pem`
- `C:\nginx\certs\bilezik.zeydozer.com\bilezik.zeydozer.com-key.pem`

## 6. Yenilemeyi kontrol edin

win-acme normalde kendi Scheduled Task kaydini olusturur. Renewal sonrasi Nginx reload'u `deploy\windows\reload-nginx.ps1` ile yapilir.

Elle test etmek icin:

```powershell
& 'C:\Program Files\win-acme\wacs.exe' --renew --verbose
cd C:\nginx
.\nginx.exe -s reload
```

## Notlar

- Nginx Windows build'i resmi olarak beta kabul edilir; kucuk/orta trafik icin kullanabilirsiniz ama daha stabil bir production ortami gerekiyorsa Linux host daha dogrudur.
- `APP_PORT` degisirse `deploy/windows/nginx/` altindaki upstream portunu da ayni degerle guncelleyin.
- Sorun aninda `C:\nginx\logs\error.log`, `docker compose logs -f app` ve `%ProgramData%\win-acme\acme-v02.api.letsencrypt.org\Log` yararlidir.
