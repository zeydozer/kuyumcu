<div class="modal fade prd-modal" id="product-update" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="product.update(this); return false" enctype="multipart/form-data">
      <input type="hidden" name="id">
      <input type="hidden" name="ctg_id_default">
      <div class="modal-header">
        <h1 class="modal-title">Ürün Düzenle</h1>
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
        <div class="prd-check form-check" style="display: none">
          <input class="form-check-input" name="photo-del" type="checkbox" id="photo-del"
            onchange="$('[id=photo]').slideToggle(150)">
          <label class="form-check-label" for="photo-del">Mevcut fotoğrafı kaldır</label>
        </div>
        <div id="photo">
          <label class="prd-field">
            <span>Fotoğraf</span>
            <input type="file" class="form-control" name="photo" accept="image/*">
          </label>
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
          <span>Pay</span>
          <input type="number" class="form-control" name="between" placeholder="0.00" step="0.01" required>
        </label>
        <div class="prd-check form-check">
          <input class="form-check-input" name="empty" type="checkbox" id="update-empty">
          <label class="form-check-label" for="update-empty">Müşteri fotoğraf yükleyebilir</label>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>
