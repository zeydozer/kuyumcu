<div class="modal fade usr-modal" id="user-update" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content" onsubmit="user.update(this); return false">
      <input type="hidden" name="id">
      <div class="modal-header">
        <h1 class="modal-title">Kullanıcı Düzenle</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        @if ($user->admin)
          <label class="usr-field">
            <span>Rol</span>
            <select name="role" class="form-control" required>
              <option value="" selected disabled>— Rol seçin</option>
              <option value="0">Personel</option>
              <option value="1">Müşteri</option>
            </select>
          </label>
          <div class="form-check admin mb-4" style="display: none">
            <input class="form-check-input" name="admin" type="checkbox" id="update-admin">
            <label class="form-check-label" for="update-admin">Admin yetkisi ver</label>
          </div>
        @else
          <input type="hidden" name="role">
        @endif
        <label class="usr-field">
          <span>İsim</span>
          <input type="text" class="form-control" name="name" placeholder="Ad soyad" required>
        </label>
        <label class="usr-field">
          <span>E-Mail</span>
          <input type="email" class="form-control" name="mail" placeholder="ornek@firma.com" required>
        </label>
        <label class="usr-field">
          <span>Yeni Şifre</span>
          <input type="password" class="form-control" name="pass" placeholder="Boş bırakılırsa değişmez">
        </label>
        <label class="usr-field">
          <span>Telefon</span>
          <input type="text" class="form-control" name="phone" placeholder="5xx xxx xx xx">
        </label>
        <label class="usr-field mb-0">
          <span>Adres</span>
          <input type="text" class="form-control" name="address" placeholder="Adres">
        </label>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <button type="submit" class="btn btn-primary">Kaydet</button>
      </div>
    </form>
  </div>
</div>
