@extends('main')

@section('title', 'Kategoriler')

@section('content')
<div class="cat-shell" id="category-container" style="display: none">
  <div class="cat-page">

    <header class="cat-header">
      <div>
        <p class="cat-kicker">Ürün yönetimi</p>
        <h1 class="cat-title">Kategoriler</h1>
      </div>
      <div class="cat-header-actions">
        <button class="cat-ghost-btn" data-bs-toggle="modal" data-bs-target="#category-search">
          <i class="bi-search"></i>
          <span>Ara</span>
        </button>
        <button class="cat-primary-btn" data-bs-toggle="modal" data-bs-target="#category-new">
          <i class="bi-plus-lg"></i>
          <span>Yeni Kategori</span>
        </button>
      </div>
    </header>

    <div class="cat-card">
      <div class="table-responsive" style="display: none">
        <table class="cat-table table mb-0">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" sort="name asc">İsim</th>
              <th scope="col">Üst Kategori</th>
              <th scope="col" sort="created_at asc">
                Ekleme
                <i class="bi-arrow-down"></i>
              </th>
              <th scope="col" sort="updated_at asc">Düzenleme</th>
              <th scope="col"></th>
            </tr>
          </thead>
          <tbody></tbody>
        </table>
      </div>
      @include('page')
    </div>

  </div>
</div>

@include('category.new')
@include('category.update')
@include('category.delete')
@include('category.search')
@endsection

@section('css')
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@500;600;700&family=Manrope:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="/css/category.css?step=3">
@endsection

@section('js')
<script src="/js/category.js?step=35"></script>
@endsection
