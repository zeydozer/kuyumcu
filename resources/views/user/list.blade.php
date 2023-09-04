@extends('main')

@section('title', 'Kullanıcılar')

@php $user = request("user") @endphp

@section('content')
<div class="container-fluid my-3" id="user-container" style="display: none">
  <div class="row">
    <div class="col">
      <div class="table-responsive" style="display: none">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col" sort="name asc">İsim</th>
              <th scope="col" sort="role asc">Rol</th>
              <th scope="col" sort="mail asc">E-Mail</th>
              <th scope="col" sort="phone asc">Telefon</th>
              <th scope="col" sort="address asc">Adres</th>
              <th scope="col" sort="created_at asc">
                Ekleme
                <i class="bi-arrow-down"></i>
              </th>
              <th scope="col" sort="updated_at asc">Düzenleme</th>
              <th scope="col">
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#user-search">
                  <i class="bi-search"></i>
                </button>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#user-new">
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
@include('user.new')
@include('user.update')
@include('user.delete')
@include('user.search')
@endsection

@section('css')
<link rel="stylesheet" href="/css/user.css?step=1">
@endsection

@section('js')
<script src="/js/user.js?step=20"></script>
@endsection