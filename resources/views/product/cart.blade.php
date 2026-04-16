<div class="modal fade prd-modal" id="product-cart" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="product.cart(this); return false" enctype="multipart/form-data">
      <div class="modal-header">
        <h1 class="modal-title">Sepete Ekle</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id">

        <div class="row g-2 mb-4" id="heights"></div>

        <div class="prd-cart-total" id="cart-total">
          <div class="prd-cart-stat">
            <div class="prd-cart-stat-label">Toplam Adet</div>
            <div class="prd-cart-stat-value"><cart-quantity>0</cart-quantity></div>
          </div>
          <div class="prd-cart-stat">
            <div class="prd-cart-stat-label">Toplam Ağırlık</div>
            <div class="prd-cart-stat-value"><cart-weight>0</cart-weight></div>
          </div>
        </div>

        <div class="prd-search-row" style="gap: 12px; margin-bottom: 20px;">
          <label class="prd-field mb-0">
            <span>Genişlik</span>
            <input type="number" class="form-control" name="width" placeholder="0.00" step="0.01" required>
          </label>
          <label class="prd-field mb-0">
            <span>Ağırlık</span>
            <input type="number" class="form-control" name="weight" placeholder="0.00" step="0.01" required>
          </label>
        </div>

        <label class="prd-field">
          <span>Not</span>
          <input type="text" class="form-control" name="note" placeholder="Sipariş notu">
        </label>

        <label class="prd-field mb-0">
          <span>Fotoğraf</span>
          <input type="file" class="form-control" name="photo">
        </label>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Sepete Ekle</button>
      </div>
    </form>
  </div>
</div>
