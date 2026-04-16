<div class="modal fade crt-modal" id="cart-order" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="cart.order(this); return false">
      <div class="modal-header">
        <h1 class="modal-title">Sipariş Oluştur</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        @if ($user->role == 0)
          <label class="crt-field">
            <span>Müşteri</span>
            <input type="text" name="user" class="form-control" placeholder="İsimle ara (en az 3 harf)" required>
          </label>
          <div id="user-results" style="display: none"></div>
        @endif
        <label class="crt-field">
          <span>Termin Tarihi</span>
          <input type="date" class="form-control" name="finished_at"
            min="{{ date('Y-m-d') }}" required>
        </label>
        <label class="crt-field mb-0">
          <span>Not</span>
          <input type="text" class="form-control" name="note" placeholder="Sipariş notu">
        </label>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Onayla</button>
      </div>
    </form>
  </div>
</div>
