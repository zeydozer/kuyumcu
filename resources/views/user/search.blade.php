<div class="modal fade" id="user-search" tabindex="-1" aria-labelledby="user-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="user-label">Kullanıcı Ara</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="sort" id="current-sort">
        <div class="input-group mb-3">
          <span class="input-group-text">Rol</span>
          <select name="role" class="form-control">
            <option value="" selected>- Rol Seçin (Yok)</option>
            <option value="0">Personel</option>
            <option value="1">Müşteri</option>
          </select>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">İsim</span>
          <input type="text" class="form-control" name="name" placeholder="İsim">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">E-Mail</span>
          <input type="text" class="form-control" name="mail" placeholder="E-Mail">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Telefon</span>
          <input type="text" class="form-control" name="phone" placeholder="Telefon">
        </div>
        <div class="input-group">
          <span class="input-group-text">Adres</span>
          <input type="text" class="form-control" name="address" placeholder="Adres">
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <input type="reset" class="btn btn-info"></input>
        <button type="submit" class="btn btn-primary">Ara</button>
      </div>
    </form>
  </div>
</div>