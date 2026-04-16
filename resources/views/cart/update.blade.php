<div class="modal fade crt-modal" id="cart-update" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="cart.update(this); return false" enctype="multipart/form-data">
      <div class="modal-header">
        <h1 class="modal-title"></h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="id">
        <input type="hidden" name="product_id">

        <div class="row g-2 mb-4" id="heights"></div>

        <div class="crt-cart-total">
          <div class="crt-cart-stat">
            <div class="crt-cart-stat-label">Toplam Adet</div>
            <div class="crt-cart-stat-value"><cart-quantity>0</cart-quantity></div>
          </div>
          <div class="crt-cart-stat">
            <div class="crt-cart-stat-label">Toplam Ağırlık</div>
            <div class="crt-cart-stat-value"><cart-weight>0</cart-weight></div>
          </div>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; margin-bottom: 20px;">
          <label class="crt-field mb-0">
            <span>Genişlik</span>
            <input type="number" class="form-control" name="width" placeholder="0.00" step="0.01" required>
          </label>
          <label class="crt-field mb-0">
            <span>Ağırlık</span>
            <input type="number" class="form-control" name="weight" placeholder="0.00" step="0.01" required>
          </label>
        </div>

        <label class="crt-field">
          <span>Not</span>
          <input type="text" class="form-control" name="note" placeholder="Sipariş notu">
        </label>

        <div class="crt-check form-check">
          <input class="form-check-input" name="photo-del" type="checkbox" id="photo-del"
            onchange="$('[id=photo]').slideToggle(150)">
          <label class="form-check-label" for="photo-del">Mevcut fotoğrafı kaldır</label>
        </div>

        <div id="photo">
          <label class="crt-field mb-0">
            <span>Fotoğraf</span>
            <input type="file" class="form-control" name="photo">
          </label>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>
