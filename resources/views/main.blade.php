@php $user = request("user") @endphp
<!DOCTYPE html>
<html lang="tr" data-bs-theme="{{ session('mode') != 'moon' ? 'light' : 'dark' }}">
<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>
    Şen Bilezik
    @hasSection('title')
      - @yield('title')
    @endif
  </title>
  <link rel="shortcut icon" href="/img/favicon.png">
  <link rel="stylesheet" href="/css/lib/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
  <link rel="stylesheet" href="/css/main.css?step=13">
  @yield('css')
</head>
<body>
  <nav class="navbar navbar-expand-sm bg-body-tertiary sticky-top">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Şen Bilezik</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarSupportedContent">
        <ul class="navbar-nav me-auto">
          @if (!$user->admin)
            <li class="nav-item">
              <a class="nav-link" href="/orders?status=0">Sipariş</a>
            </li>
          @else
            <li class="nav-item dropdown">
              <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                Sipariş
              </a>
              <ul class="dropdown-menu">
                <li><a class="dropdown-item" href="/orders?status=0">Liste</a></li>
                <li><a class="dropdown-item" href="/order-fast">Hızlı Giriş</a></li>
              </ul>
            </li>
          @endif
          @if ($user->admin)
            <li class="nav-item">
              <a class="nav-link" href="/categories">Kategori</a>
            </li>
          @endif
          @if (($user->role == 0 && $user->admin) || $user->role == 1)
            <li class="nav-item">
              <a class="nav-link" href="/products">Ürün</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/carts">
                Sepet (<cart-count>{{ $user->carts_count }}</cart-count>)
              </a>
            </li>
            @if ($user->admin)
              <li class="nav-item">
                <a class="nav-link" href="/users">Kullanıcılar</a>
              </li>
            @endif
          @endif
        </ul>
        <ul class="navbar-nav navbar-right">
          <li>
            <a class="nav-link" id="mode" href="#">
              @if (session('mode') != 'moon')
                <i class="bi-moon"></i>
              @else
                <i class="bi-sun"></i>
              @endif
            </a>
          </li>
          <li>
            <a class="nav-link" href="/logout">Çıkış</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="toast-container position-fixed top-50 start-50 translate-middle p-3 alert">
    <div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2000">
      <div class="toast-body"></div>
    </div>
  </div>
  <div class="loading d-flex fade justify-content-center position-fixed w-100 h-100 start-0 align-items-center bg-body-tertiary z-n1">
    <div class="spinner-border" role="status" style="width: 5rem; height: 5rem">
      <span class="visually-hidden">Yükleniyor...</span>
    </div>
  </div>
  @yield('content')
  <script>
    const TOKEN = '{{ csrf_token() }}'
    const USER = JSON.parse('{!! $user !!}')
    @php $types = config("const.productType") @endphp
    const PRODUCT_TYPE = '{{ implode(",", array_keys($types)) }}'.split(',')
    const HEIGHTS = JSON.parse('{!! json_encode($types) !!}')
    @php $status = json_encode(config("const.status")) @endphp
    const ORDER_STATUS = JSON.parse('{!! $status !!}')
  </script>
  <script src="/js/lib/jquery-3.7.0.min.js"></script>
  <script src="/js/lib/bootstrap.bundle.min.js"></script>
  <script src="/js/main.js?step=36"></script>
  @yield('js')
</body>
</html>