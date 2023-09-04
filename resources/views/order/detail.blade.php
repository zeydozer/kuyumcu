<div class="modal fade" id="order-detail" tabindex="-1" aria-labelledby="order-label" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="order-label">Sipariş Detayı <order-no></order-no></h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <div class="table-responsive">
          @foreach ($types as $name => $type)
            <table class="table table-striped" id="{{ $name }}">
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
                </tr>
              </thead>
              <tbody class="table-group-divider"></tbody>
              <thead class="table-group-divider">
                <tr>
                  <th scope="col" colspan="{{ 5 + $colspan}}">
                    Toplam
                  </th>
                  <th></th>
                  <th><total-quantity></total-quantity></th>
                  <th><total-weight></total-weight></th>
                </tr>
              </thead>
            </table>
          @endforeach
        </div>
        <div class="row align-items-center d-none" id="order-total">
          <div class="col text-truncate text-nowrap">
            <b>Toplam Adet</b>
          </div>
          <div class="col">
            <total-quantity></total-quantity>
          </div>
          <div class="col text-truncate text-nowrap">
            <b>Toplam Ağırlık</b>
          </div>
          <div class="col">
            <total-weight></total-weight>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Kapat</a>
        <a href="#" class="btn btn-info edit">Düzenle</a>
      </div>
    </div>
  </div>
</div>