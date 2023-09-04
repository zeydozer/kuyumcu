<div class="modal fade" id="category-update" tabindex="-1" aria-labelledby="category-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="category.update(this); return false">
      <input type="hidden" name="id">
      <input type="hidden" name="root_id_default">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="category-label">Kategori Düzenle</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <div class="input-group mb-3">
          <span class="input-group-text">İsim</span>
          <input type="text" class="form-control" name="name" placeholder="İsim" required>
        </div>  
        <div class="input-group">
          <span class="input-group-text">Üst Kategori</span>
          <input type="text" name="root" class="form-control" placeholder="Kategori İsmi İle Ara">
        </div>
        <div id="root-results" class="mt-3" style="display: none"></div> 
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>