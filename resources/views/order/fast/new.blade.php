@extends('main')

@section('title', 'Hızlı Sipariş')

@php $types = config("const.productType") @endphp

@section('content')
<form class="fst-shell" id="fast-container" onsubmit="fast.order(this); return false" enctype="multipart/form-data">
  <div class="fst-page">

    <header class="fst-header">
      <div>
        <p class="fst-kicker">Hızlı işlem</p>
        <h1 class="fst-title">Sipariş Oluştur</h1>
      </div>
    </header>

    <div class="fst-alert">
      Bu sayfaya giriş yaptıktan <b>sonra</b> sepette işlem yapmayınız.
      Aksi takdirde hata alacak ve sipariş kaydedilmeyecektir veya sepetin son hali ile kaydedilecektir.
      Sonradan sepette işlem yaptıysanız sayfayı yenileyerek devam ediniz.
    </div>

    <div class="fst-layout">
      <div class="fst-panel" id="info">
        <div class="fst-panel-body">
          <div class="input-group">
            <span class="input-group-text">Müşteri</span>
            <input type="text" name="user" class="form-control" placeholder="İsimle ara" required>
          </div>
          <div id="user-results" style="display: none"></div>
          <div class="input-group">
            <span class="input-group-text">Termin</span>
            <input type="date" class="form-control" name="finished_at"
              min="{{ date('Y-m-d') }}" required>
          </div>
          <div class="input-group">
            <span class="input-group-text">Not</span>
            <input type="text" class="form-control" name="note" placeholder="Sipariş notu">
          </div>
        </div>
      </div>

      <div class="fst-panel" id="products">
        <div class="fst-panel-body">
          <div class="input-group">
            <span class="input-group-text">Ürün</span>
            <input type="text" name="product" class="form-control" placeholder="İsimle ara">
          </div>
          <div class="fst-product-scroll">
            <table class="fst-search-table table mb-0">
              <thead>
                <tr>
                  <th scope="col">Ürün</th>
                  <th scope="col">Genişlik</th>
                  <th scope="col">Ağırlık</th>
                  <th scope="col">Vitrin</th>
                  <th scope="col">Ekle</th>
                </tr>
              </thead>
              <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>

    <div class="fst-section">
      <ul class="nav fst-tabs d-none" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a href="#" class="nav-link active" id="cart-tab"
            data-bs-toggle="tab" data-bs-target="#cart-tab-pane"
            role="tab" aria-controls="cart-tab-pane" aria-selected="true">Sepet</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#" class="nav-link" id="trash-tab"
            data-bs-toggle="tab" data-bs-target="#trash-tab-pane"
            role="tab" aria-controls="trash-tab-pane" aria-selected="false">Silinen</a>
        </li>
      </ul>
      <div class="fst-table-card">
        <div class="tab-content" id="myTabContent">
          <div class="tab-pane fade show active" id="cart-tab-pane" role="tabpanel" aria-labelledby="cart-tab" tabindex="0">
            <div class="table-responsive">
              @foreach ($types as $name => $type)
                <table class="fst-table table mb-0 {{ $name }}">
                  <thead>
                    <tr>
                      <th scope="col">#</th>
                      <th scope="col">Ürün</th>
                      <th scope="col">Not</th>
                      <th scope="col">Genişlik</th>
                      @php $colspan = 0 @endphp
                      @for ($i = $type['min']; $i <= $type['max']; $i += $type['between'])
                        <th scope="col">{{ $i }}</th>
                        @php $colspan++ @endphp
                      @endfor
                      <th scope="col">Ağırlık</th>
                      <th scope="col">Toplam Adet</th>
                      <th scope="col">Toplam Ağırlık</th>
                      <th scope="col"></th>
                    </tr>
                  </thead>
                  <tbody class="table-group-divider"></tbody>
                  <thead class="table-group-divider">
                    <tr>
                      <th scope="col" colspan="{{ 4 + $colspan }}">Toplam</th>
                      <th></th>
                      <th><total-quantity></total-quantity></th>
                      <th><total-weight></total-weight></th>
                      <th></th>
                    </tr>
                  </thead>
                </table>
              @endforeach
            </div>
          </div>
          <div class="tab-pane fade" id="trash-tab-pane" role="tabpanel" aria-labelledby="trash-tab" tabindex="0">b</div>
        </div>
      </div>
    </div>

    <div class="fst-actions">
      <button class="btn btn-primary" type="submit" style="display: none">Kaydet</button>
    </div>

  </div>
</form>

@include('cart.image')
@endsection

@section('css')
<link rel="stylesheet" href="/css/fast/new.css?step=12">
@endsection

@section('js')
<script src="/js/fast/new.js?step=17"></script>
@endsection
