@extends('main')

@section('title', 'Kategoriler')

@section('content')
<div class="container-fluid my-3" id="category-container" style="display: none">
  <div class="row">
    <div class="col">
      <div class="table-responsive" style="display: none">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" sort="name asc">İsim</th>
              <th scope="col">Üst</th>
              <th scope="col" sort="created_at asc">
                Ekleme
                <i class="bi-arrow-down"></i>
              </th>
              <th scope="col" sort="updated_at asc">Düzenleme</th>
              <th scope="col">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#category-search">
                  <i class="bi-search"></i>
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#category-new">
                  <i class="bi-plus-lg"></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody class="table-group-divider"></tbody>
        </table>
      </div>
    </div>
  </div>
  @include('page')
</div>
@include('category.new')
@include('category.update')
@include('category.delete')
@include('category.search')
@endsection

@section('css')
<link rel="stylesheet" href="/css/category.css?step=2">
@endsection

@section('js')
<script src="/js/category.js?step=35"></script>
@endsection