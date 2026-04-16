<div class="modal fade cat-modal" id="category-new" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="category.new(this); return false">
      <div class="modal-header">
        <h1 class="modal-title">Kategori Ekle</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <label class="cat-field">
          <span>İsim</span>
          <input type="text" class="form-control" name="name" placeholder="Kategori adı" required>
        </label>
        <label class="cat-field mb-0">
          <span>Üst Kategori</span>
          <input type="text" name="root" class="form-control" placeholder="Kategori ismiyle ara (en az 3 harf)">
        </label>
        <div id="root-results" class="mt-2" style="display: none"></div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>
