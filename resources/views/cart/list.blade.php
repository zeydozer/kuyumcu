@extends('main')

@section('title', 'Sepet')

@php 
  $user = request("user");
  $types = config("const.productType");
@endphp

@section('content')
<div class="container-fluid my-3" id="cart-container" style="display: none">
  <div class="row">
    <div class="col">
      <div class="table-responsive" style="display: none">
        @foreach ($types as $name => $type)
          <table class="table table-striped" id="{{ $name }}" style="display: none">
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
                <th scope="col">
                  <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#cart-order">
                    <i class="bi-check2-all"></i>
                  </button>
                </th>
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
  </div>
</div>
@include('cart.delete')
@include('cart.image')
@include('cart.update')
@include('cart.order')
@endsection

@section('css')
<link rel="stylesheet" href="/css/cart.css?step=2">
@endsection

@section('js')
<script src="/js/cart.js?step=45"></script>
@endsection