<div class="modal fade" id="product-cart" tabindex="-1" aria-labelledby="product-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="product.cart(this); return false" enctype="multipart/form-data">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="product-label">Sepete Ekle</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id">
        <div class="row g-2 mb-3" id="heights"></div>
        <div class="input-group mb-3">
          <span class="input-group-text">Genişlik</span>
          <input type="number" class="form-control" name="width" placeholder="Genişlik" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Ağırlık</span>
          <input type="number" class="form-control" name="weight" placeholder="Ağırlık" step="0.01" required>
        </div>
        <div class="row mb-3 align-items-center" id="cart-total">
          <div class="col text-truncate text-nowrap">
            <b>Toplam Adet</b>
          </div>
          <div class="col">
            <cart-quantity></cart-quantity>
          </div>
          <div class="col text-truncate text-nowrap">
            <b>Toplam Ağırlık</b>
          </div>
          <div class="col">
            <cart-weight></cart-weight>
          </div>
        </div>
        <div class="input-group">
          <span class="input-group-text">Not</span>
          <input type="text" class="form-control" name="note" placeholder="Not">
        </div>
        <div class="input-group mt-3">
          <span class="input-group-text">Fotoğraf</span>
          <input type="file" class="form-control" name="photo">
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Ekle</button>
      </div>
    </form>
  </div>
</div>