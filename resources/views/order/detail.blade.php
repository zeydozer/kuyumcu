<div class="modal fade ord-modal" id="order-detail" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title">Sipariş Detayı <order-no></order-no></h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <div class="table-responsive" style="display: none">
          @foreach ($types as $name => $type)
            <table class="ord-detail-table table mb-0" id="{{ $name }}">
              <thead>
                <tr>
                  <th scope="col">#</th>
                  <th scope="col">Ürün</th>
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
                  <th scope="col" colspan="{{ 3 + $colspan }}">Toplam</th>
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
        <a href="#" class="btn btn-primary edit">Düzenle</a>
      </div>
    </div>
  </div>
</div>
