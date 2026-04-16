@extends('main')

@section('title', 'Ürünler')

@php $user = request("user") @endphp

@section('content')
<div class="prd-shell" id="product-container" style="display: none">
  <div class="prd-page">

    <header class="prd-header">
      <div>
        <p class="prd-kicker">Ürün kataloğu</p>
        <h1 class="prd-title">Ürünler</h1>
      </div>
    </header>

    <div class="prd-layout">

      <aside class="prd-sidebar">
        @if ($user->role == 0)
          <button class="prd-add-btn" data-bs-toggle="modal" data-bs-target="#product-new">
            <i class="bi-plus-lg"></i>
            <span>Ürün Ekle</span>
          </button>
        @endif

        <form id="product-search" class="prd-search-form">
          <div class="prd-search-row">
            <select name="order[0]" class="form-control">
              <option value="created_at" selected>Tarih</option>
              <option value="name">İsim</option>
              <option value="width">Genişlik</option>
              <option value="weight">Ağırlık</option>
            </select>
            <select name="order[1]" class="form-control">
              <option value="desc" selected>Azalan</option>
              <option value="asc">Artan</option>
            </select>
          </div>
          <input type="search" class="form-control" placeholder="Ürün ara…" name="name">
          <input type="hidden" name="page" id="current-page">
          <input type="hidden" name="ctg">
        </form>

        <div>
          <p class="prd-cat-label">Kategoriler</p>
          <div id="categories">
            <div class="spinner-border spinner-border-sm text-secondary" role="status">
              <span class="visually-hidden">Yükleniyor…</span>
            </div>
          </div>
        </div>
      </aside>

      <main class="prd-main">
        <div class="row g-3" id="products"></div>
        @include('page')
      </main>

    </div>
  </div>
</div>

@if ($user->role == 0)
  @include('product.new')
  @include('product.update')
  @include('product.delete')
@endif
@include('product.cart')
@endsection

@section('css')
<link rel="stylesheet" href="/css/product.css?step=5">
@endsection

@section('js')
<script src="/js/product.js?step=47"></script>
@endsection
