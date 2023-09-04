<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Sipariş - Giriş</title>
  <link rel="stylesheet" href="/css/lib/bootstrap.min.css">
  <link rel="shortcut icon" href="/img/favicon.png">
</head>

<body>
  <div class="container mt-3">
    <div class="row justify-content-center">
      <div class="col-md-6">
        <form id="login">
          <input type="email" class="form-control w-100 mb-3" name="mail" placeholder="E-Mail" required>
          <input type="password" class="form-control w-100 mb-3" name="pass" placeholder="Şifre" required>
          <div class="row align-items-center">
            <div class="col-6">
              <button type="submit" class="btn btn-primary w-100">Giriş</button>
            </div>
            <div class="col-6 text-end">
              <a href="#" class="text-decoration-none"
                onclick="alertCustom('warning', 'Yapım aşamasında.'); return false">
                Şifremi Unuttum
              </a>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>
  <div class="toast-container position-fixed top-50 start-50 translate-middle p-3">
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true">
      <div class="toast-body"></div>
    </div>
  </div>
  <script>
    const TOKEN = '{{ csrf_token() }}'
  </script>
  <script src="/js/lib/jquery-3.7.0.min.js"></script>
  <script src="/js/lib/bootstrap.bundle.min.js"></script>
  <script src="/js/login.js"></script>
</body>

</html>