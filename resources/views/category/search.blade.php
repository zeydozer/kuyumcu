<div class="modal fade cat-modal" id="category-search" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title">Kategori Ara</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="sort" id="current-sort">
        <label class="cat-field">
          <span>İsim</span>
          <input type="text" class="form-control" name="name" placeholder="Kategori adı">
        </label>
        <label class="cat-field mb-0">
          <span>Üst Kategori</span>
          <input type="text" class="form-control" name="root_name" placeholder="Üst kategori adı">
        </label>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <input type="reset" class="btn btn-info" value="Temizle">
        <button type="submit" class="btn btn-primary">Ara</button>
      </div>
    </form>
  </div>
</div>
