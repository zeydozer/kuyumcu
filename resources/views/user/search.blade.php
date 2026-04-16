<div class="modal fade usr-modal" id="user-search" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title">Kullanıcı Ara</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="sort" id="current-sort">
        <label class="usr-field">
          <span>Rol</span>
          <select name="role" class="form-control">
            <option value="" selected>— Tümü</option>
            <option value="0">Personel</option>
            <option value="1">Müşteri</option>
          </select>
        </label>
        <label class="usr-field">
          <span>İsim</span>
          <input type="text" class="form-control" name="name" placeholder="Ad soyad">
        </label>
        <label class="usr-field">
          <span>E-Mail</span>
          <input type="text" class="form-control" name="mail" placeholder="ornek@firma.com">
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
        <input type="reset" class="btn btn-info" value="Temizle">
        <button type="submit" class="btn btn-primary">Ara</button>
      </div>
    </form>
  </div>
</div>
