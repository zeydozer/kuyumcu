@extends('main')

@section('title', 'Kullanıcılar')

@php $user = request("user") @endphp

@section('content')
<div class="usr-shell" id="user-container" style="display: none">
  <div class="usr-page">

    <header class="usr-header">
      <div>
        <p class="usr-kicker">Yönetim paneli</p>
        <h1 class="usr-title">Kullanıcılar</h1>
      </div>
      <div class="usr-header-actions">
        <button class="usr-ghost-btn" data-bs-toggle="modal" data-bs-target="#user-search">
          <i class="bi-search"></i>
          <span>Ara</span>
        </button>
        <button class="usr-primary-btn" data-bs-toggle="modal" data-bs-target="#user-new">
          <i class="bi-plus-lg"></i>
          <span>Yeni Kullanıcı</span>
        </button>
      </div>
    </header>

    <div class="usr-card">
      <div class="table-responsive" style="display: none">
        <table class="usr-table table mb-0">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" sort="name asc">İsim / E-Mail</th>
              <th scope="col" sort="role asc">Rol</th>
              <th scope="col" sort="phone asc">Telefon / Adres</th>
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

@include('user.new')
@include('user.update')
@include('user.delete')
@include('user.search')
@endsection

@section('css')
<link rel="stylesheet" href="/css/user.css?step=2">
@endsection

@section('js')
<script src="/js/user.js?step=21"></script>
@endsection
