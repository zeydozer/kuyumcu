@extends('main')

@section('title', 'Hızlı Sipariş')

@php $types = config("const.productType") @endphp

@section('content')
<form class="container-fluid my-3" onsubmit="fast.order(this); return false"
  id="fast-container" enctype="multipart/form-data">
  <div class="alert alert-warning" role="alert">
    Bu sayfaya giriş yaptıktan <b>sonra</b> sepette işlem yapmayınız.
    Aksi takdirde hata alacak ve sipariş kaydedilmeyecektir veya sepetin son hali ile kaydedilecektir.
    Sonradan sepette işlem yaptıysanız sayfayı yenileyerek devam ediniz.
  </div>
  <div class="row mb-3">
    <div class="col-md-6" id="info">
      <div class="input-group mb-3">
        <span class="input-group-text">Müşteri</span>
        <input type="text" name="user" class="form-control" placeholder="Müşteri İsmi İle Ara" required>
      </div>
      <div id="user-results" class="my-3" style="display: none"></div>
      <div class="input-group mb-3">
        <span class="input-group-text">Termin</span>
        <input type="date" class="form-control" name="finished_at" 
          min="{{ date('Y-m-d') }}" placeholder="Termin" required>
      </div>
      <div class="input-group">
        <span class="input-group-text">Not</span>
        <input type="text" class="form-control" name="note" placeholder="Not">
      </div>
    </div>
    <div class="col-md-6 mt-3 mt-sm-0" id="products">
      <div class="input-group mb-3">
        <span class="input-group-text">Ürün</span>
        <input type="text" name="product" class="form-control" placeholder="Ürün İsmi İle Ara">
      </div>
      <div class="table-responsive">
        <table class="table table-striped mb-0">
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
  <div class="row">
    <div class="col-12">
      <ul class="nav nav-tabs mb-3 d-none" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <a href="#" class="nav-link active" id="cart-tab" data-bs-toggle="tab" data-bs-target="#cart-tab-pane" type="button" role="tab" aria-controls="cart-tab-pane" aria-selected="true">Sepet</a>
        </li>
        <li class="nav-item" role="presentation">
          <a href="#" class="nav-link" id="trash-tab" data-bs-toggle="tab" data-bs-target="#trash-tab-pane" type="button" role="tab" aria-controls="trash-tab-pane" aria-selected="false">Silinen</a>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="cart-tab-pane" role="tabpanel" aria-labelledby="cart-tab" tabindex="0">
          <div class="table-responsive">
            @foreach ($types as $name => $type)
              <table class="table table-striped {{ $name }}">
                <thead>
                  <tr>
                    <th scope="col">#</th>
                    <th scope="col">Ürün</th>
                    <th scope="col">Fotoğraf</th>
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
                  <th scope="col" colspan="{{ 5 + $colspan}}">
                    Toplam
                  </th>
                  <th></th>
                  <th><total-quantity></total-quantity></th>
                  <th><total-weight></total-weight></th>
                  <th></th>
                </thead>
              </table>
            @endforeach
          </div>
        </div>
        <div class="tab-pane fade" id="trash-tab-pane" role="tabpanel" aria-labelledby="trash-tab" tabindex="0">b</div>
      </div>
    </div>
  </div>
  <button class="btn btn-primary float-end" type="submit" style="display: none">Kaydet</button>
</form>
@include('cart.image')
@endsection

@section('css')
<link rel="stylesheet" href="/css/fast/new.css?step=2">
@endsection

@section('js')
<script src="/js/fast/new.js?step=13"></script>
@endsection