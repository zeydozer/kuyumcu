<div class="modal fade" id="product-update" tabindex="-1" aria-labelledby="product-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="product.update(this); return false" enctype="multipart/form-data">
      <input type="hidden" name="id">
      <input type="hidden" name="ctg_id_default">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="product-label">Ürün Düzenle</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text">İsim</span>
          <input type="text" class="form-control" name="name" placeholder="İsim" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Kategori</span>
          <input type="text" name="ctg" class="form-control" placeholder="Kategori İsmi İle Ara">
        </div>
        <div id="ctg-results" class="my-3" style="display: none"></div>
        <div class="form-check mb-3">
          <input class="form-check-input" name="photo-del" type="checkbox" id="photo-del" 
            onchange="$('[id=photo]').slideToggle(150)">
          <label class="form-check-label" for="photo-del">
            Fotoğrafı kaldır.
          </label>
        </div>
        <div class="input-group mb-3" id="photo">
          <span class="input-group-text">Fotoğraf</span>
          <input type="file" class="form-control" name="photo" placeholder="Fotoğraf" accept="image/*">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Genişlik</span>
          <input type="number" class="form-control" name="width" placeholder="Genişlik" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Ağırlık</span>
          <input type="number" class="form-control" name="weight" placeholder="Ağırlık" step="0.01" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Pay</span>
          <input type="number" class="form-control" name="between" placeholder="Pay" step="0.01" required>
        </div>
        <div class="form-check">
          <input class="form-check-input" name="empty" type="checkbox" id="update-empty">
          <label class="form-check-label" for="update-empty">Müşteri fotoğraf yükleyebilir.</label>
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>