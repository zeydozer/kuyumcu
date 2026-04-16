<!DOCTYPE html>
<html lang="tr">

<head>
  <meta charset="UTF-8">
  <meta http-equiv="X-UA-Compatible" content="IE=edge">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="theme-color" content="#17120d">
  <title>Bilezik - Giri&#351;</title>
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="/css/lib/bootstrap.min.css">
  <link rel="stylesheet" href="/css/login.css?step=1">
  <link rel="shortcut icon" href="/img/favicon.png">
</head>

<body>
  <div class="login-shell">
    <div class="login-layout">
      <section class="login-hero">
        <div class="login-hero-copy">
          <p class="login-kicker">Bilezik operasyon paneli</p>
          <h1>At&#246;lye, stok ve sipari&#351; ak&#305;&#351;&#305; ayn&#305; ekranda.</h1>
          <p class="login-copy">
            &#220;r&#252;n kayd&#305;, sepet haz&#305;rlama ve sipari&#351; y&#246;netimini tek panelden takip edin.
            Yetkili ekipler i&#231;in sade ama g&#252;&#231;l&#252; bir giri&#351; deneyimi.
          </p>
        </div>

        <div class="hero-panel">
          <div class="hero-panel-head">
            <span class="hero-badge">Canl&#305; ak&#305;&#351;</span>
            <span class="hero-dot"></span>
          </div>
          <div class="hero-timeline">
            <div class="hero-item">
              <small>01</small>
              <div>
                <strong>Gelen sipari&#351;ler</strong>
                <p>&#214;ncelikli i&#351;leri ka&#231;&#305;rmadan s&#305;ra i&#231;inde y&#246;netin.</p>
              </div>
            </div>
            <div class="hero-item">
              <small>02</small>
              <div>
                <strong>Stok ve &#252;r&#252;n hareketi</strong>
                <p>&#220;r&#252;n, kategori ve sepet bilgisini ayn&#305; ak&#305;&#351;ta tutun.</p>
              </div>
            </div>
            <div class="hero-item">
              <small>03</small>
              <div>
                <strong>Haz&#305;rlama ve teslim</strong>
                <p>Sipari&#351; durumunu ekibin g&#246;rece&#287;i &#351;ekilde netle&#351;tirin.</p>
              </div>
            </div>
          </div>
        </div>
      </section>

      <main class="login-card-wrap">
        <section class="login-card">
          <div class="login-card-top">
            <h2 class="mt-0">Giri&#351; yap&#305;n</h2>
            <p>Kurumsal e-posta adresiniz ve parolan&#305;z ile devam edin.</p>
          </div>

          <form id="login" class="login-form" novalidate>
            <label class="field">
              <span>E-posta</span>
              <input type="email" class="form-control" name="mail" placeholder="ornek@firma.com" autocomplete="username" required>
            </label>

            <label class="field">
              <span>Parola</span>
              <div class="password-field">
                <input type="password" class="form-control" name="pass" placeholder="Parolan&#305;z&#305; girin" autocomplete="current-password" required>
                <button type="button" class="password-toggle" data-password-toggle aria-label="Parolay&#305; g&#246;ster">
                  G&#246;ster
                </button>
              </div>
            </label>

            <div id="login-feedback" class="login-feedback" role="alert" aria-live="polite"></div>

            <button type="submit" class="btn login-submit">Giri&#351;</button>

            <div class="login-actions">
              <button type="button" class="login-link" data-forgot-password>&#350;ifremi unuttum</button>
            </div>
          </form>
        </section>
      </main>
    </div>
  </div>

  <div class="toast-container position-fixed top-0 end-0 p-3">
    <div class="toast border-0 shadow-sm" role="alert" aria-live="assertive" aria-atomic="true" data-bs-delay="2200">
      <div class="toast-body"></div>
    </div>
  </div>

  <script>
    const TOKEN = '{{ csrf_token() }}'
  </script>
  <script src="/js/lib/jquery-3.7.0.min.js"></script>
  <script src="/js/lib/bootstrap.bundle.min.js"></script>
  <script src="/js/login.js?step=1"></script>
</body>

</html>
