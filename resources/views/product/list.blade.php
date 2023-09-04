@extends('main')

@section('title', 'Ürünler')

@php $user = request("user") @endphp

@section('content')
<div class="container-fluid my-3" id="product-container" style="display: none">
  <div class="row g-3">
    <div class="col-xl-2 col-md-3">
      @if ($user->role == 0)
        <button class="btn btn-primary w-100 mb-3 text-start d-flex align-items-center" data-bs-toggle="modal" data-bs-target="#product-new">
          <i class="bi-plus-lg me-1"></i>
          <span>Ürün Ekle</span>
        </button>
      @endif
      <form class="row g-3 mb-3" id="product-search">
        <div class="col">
          <select name="order[0]" class="form-control">
            <option value="created_at" selected>Tarih</option>
            <option value="name">İsim</option>
            <option value="width">Genişlik</option>
            <option value="weight">Ağırlık</option>
          </select>
        </div>
        <div class="col">
          <select name="order[1]" class="form-control">
            <option value="desc" selected>Azalan</option>
            <option value="asc">Artan</option>
          </select>
        </div>
        <div class="col-12">
          <input type="search" class="form-control" placeholder="Ürün Ara" name="name">
        </div>
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="ctg">
      </form>
      <h5 class="mb-3">Kategoriler</h5>
      <div id="categories">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
    <div class="col-xl-10 col-md-9">
      <div class="row g-3" id="products"></div>
      @include('page')
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
<link rel="stylesheet" href="/css/product.css?step=4">
@endsection

@section('js')
<script src="/js/product.js?step=44"></script>
@endsection