# MySQL init

Bu projede migration dosyalari olmadigi icin ilk kurulumda SQL dump import etmeniz gerekebilir.

Kullanim:

- Dump dosyanizi `docker/mysql/init/` altina koyun.
- Desteklenen tipler: `.sql`, `.sql.gz`, `.sh`
- Temiz import icin eski volume'u silin: `docker compose down -v`
- Sonra sistemi tekrar kaldirin: `docker compose up --build`

Not:

- `docker/mysql/init/` klasoru bos kalabilir. Docker Compose ilk calistirmada bu dizini host tarafinda olusturur.
