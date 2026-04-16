<div class="modal fade prd-modal" id="product-new" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="product.new(this); return false" enctype="multipart/form-data">
      <div class="modal-header">
        <h1 class="modal-title">Ürün Ekle</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <label class="prd-field">
          <span>İsim</span>
          <input type="text" class="form-control" name="name" placeholder="Ürün adı" required>
        </label>
        <label class="prd-field">
          <span>Kategori</span>
          <input type="text" name="ctg" class="form-control" placeholder="Kategori ismiyle ara (en az 3 harf)">
        </label>
        <div id="ctg-results" style="display: none"></div>
        <label class="prd-field">
          <span>Fotoğraf</span>
          <input type="file" class="form-control" name="photo" accept="image/*">
        </label>
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
          <span>Pay</span>
          <input type="number" class="form-control" name="between" placeholder="0.00" step="0.01" required>
        </label>
        <div class="prd-check form-check">
          <input class="form-check-input" name="empty" type="checkbox" id="new-empty">
          <label class="form-check-label" for="new-empty">Müşteri fotoğraf yükleyebilir</label>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>
