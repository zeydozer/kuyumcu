<div class="modal fade" id="cart-order" tabindex="-1" aria-labelledby="cart-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="cart.order(this); return false">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="cart-label">Sipariş</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        @if ($user->role == 0)
          <div class="input-group mb-3">
            <span class="input-group-text">Müşteri</span>
            <input type="text" name="user" class="form-control" placeholder="Müşteri İsmi İle Ara" required>
          </div>
          <div id="user-results" class="my-3" style="display: none"></div>
        @endif
        <div class="input-group mb-3">
          <span class="input-group-text">Termin</span>
          <input type="date" class="form-control" name="finished_at" 
            min="{{ date('Y-m-d') }}" placeholder="Termin" required>
        </div>
        <div class="input-group">
          <span class="input-group-text">Not</span>
          <input type="text" class="form-control" name="note" placeholder="Not">
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Onayla</button>
      </div>
    </form>
  </div>
</div>