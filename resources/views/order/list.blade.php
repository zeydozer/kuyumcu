@extends('main')

@section('title', 'Siparişler')

@php 
  $user = request("user");
  $statuses = config("const.status");
  $types = config("const.productType");
@endphp

@section('content')
<div class="container-fluid my-3" id="order-container" style="display: none">
  <div class="row">
    <div class="col">      
      <ul class="nav nav-tabs">
        <li class="nav-item">
          <a class="nav-link {{ request('status') == '' ? 'active' : null }}" onclick="order.tab('')">
            Tümü
          </a>
        </li>
        @for ($i = count($statuses) - 2; $i >= -1; $i--)
        <li class="nav-item">
          <a class="nav-link {{ request('status') == (string) $i ? 'active' : null }}" onclick="order.tab('{{ $i }}')">
            {{ ucfirst($statuses[$i]['name']) }}
          </a>
        </li>
        @endfor
      </ul>
      <div class="table-responsive" style="display: none">
        <table class="table table-striped mb-0">
          <thead>
            <tr>
              @if ($user->role == 0 && $user->admin)
                <th scope="col">
                  <input name="order-all" type="checkbox">
                </th>
              @endif
              <th scope="col">#</th>
              <th scope="col" sort="created_at asc">
                Tarih
                <i class="bi-arrow-down"></i>
              </th>
              <th scope="col" sort="id asc">Numara</th>
              <th scope="col">Müşteri</th>
              <th scope="col">Kullanıcı</th>
              <th scope="col" sort="finished_at asc">Termin</th>
              <th scope="col" sort="note asc">Not</th>
              <th scope="col" sort="quantity asc">Adet</th>
              <th scope="col" sort="weight asc">Ağırlık</th>
              <th scope="col" sort="status asc">Durum</th>
              <th scope="col">
                @if (request("status") == "0")
                  @if ($user->role == 0 && $user->admin)
                    <div class="btn-group" id="report">
                      <button type="button" class="btn btn-primary dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi-list-check"></i>
                      </button>
                      <ul class="dropdown-menu">
                        <li>
                          <a class="dropdown-item" onclick="order.report('product'); return false">
                            Ürün
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" onclick="order.report('product&only=special'); return false">
                            Ürün Özel
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" onclick="order.report('product&only=special-out'); return false">
                            Ürün Özelsiz
                          </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                          <a class="dropdown-item" onclick="order.report('customer'); return false">
                            Müşteri
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" onclick="order.report('customer&only=special'); return false">
                            Müşteri Özel
                          </a>
                        </li>
                        <li>
                          <a class="dropdown-item" onclick="order.report('customer&only=special-out'); return false">
                            Müşteri Özelsiz
                          </a>
                        </li>
                      </ul>
                    </div>
                  @elseif ($user->role == 1)
                    <button class="btn btn-primary" onclick="order.report('customer')">
                      <i class="bi-list-check"></i>
                    </button>
                  @endif
                @endif
                @if ($user->role == 0 && $user->admin)
                  <div class="btn-group" id="status" style="display: none">
                    <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                      <i class="bi-three-dots"></i>
                    </button>
                    <ul class="dropdown-menu">
                      @for ($i = count($statuses) - 2; $i >= -1; $i--)
                        <li>
                          <a class="dropdown-item text-capitalize" onclick="order.statusCheck('{{ $i }}', this); return false">
                            {{ $statuses[$i]['name'] }}
                          </a>
                        </li>
                      @endfor
                    </ul>
                  </div>
                @endif
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#order-search">
                  <i class="bi-search"></i>
                </button>
              </th>
            </tr>
          </thead>
          <tbody class="table-group-divider"></tbody>
          <thead class="table-group-divider">
            <tr>
              <th scope="col" colspan="{{ $user->role == 0 ? 8 : 7 }}">
                Toplam
              </th>
              <th><total-quantity></total-quantity></th>
              <th><total-weight></total-weight></th>
              <th colspan="2"></th>
            </tr>
          </thead>
        </table>
      </div>
    </div>
  </div>
  @include('page')
</div>
@include('order.search')
@include('order.delete')
@include('order.detail')
@include('cart.image')
@endsection

@section('css')
<link rel="stylesheet" href="/css/order.css?step=2">
@endsection

@section('js')
<script src="/js/order.js?step=12"></script>
@endsection