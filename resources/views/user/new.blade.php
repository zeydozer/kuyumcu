<div class="modal fade" id="user-new" tabindex="-1" aria-labelledby="user-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="user.new(this); return false">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="user-label">Kullanıcı Ekle</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        @if ($user->admin)
          <div class="input-group mb-3">
            <span class="input-group-text">Rol</span>
            <select name="role" class="form-control" required>
              <option value="" selected disabled>- Rol Seçin</option>
              <option value="0">Personel</option>
              <option value="1">Müşteri</option>
            </select>
          </div>
          <div class="form-check admin mb-3" style="display: none">
            <input class="form-check-input" name="admin" type="checkbox" id="new-admin">
            <label class="form-check-label" for="new-admin">Admin</label>
          </div>
        @else
          <input type="hidden" name="role">
        @endif
        <div class="input-group mb-3">
          <span class="input-group-text">İsim</span>
          <input type="text" class="form-control" name="name" placeholder="İsim" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">E-Mail</span>
          <input type="email" class="form-control" name="mail" placeholder="E-Mail" required>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Şifre</span>
          <input type="password" class="form-control" name="pass" placeholder="Şifre" required>
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
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>