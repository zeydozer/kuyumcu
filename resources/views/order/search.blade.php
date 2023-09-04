<div class="modal fade" id="order-search" tabindex="-1" aria-labelledby="order-label" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title fs-5" id="order-label">Sipariş Ara</h1>
        <a type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="sort" id="current-sort">
        <div class="input-group mb-3">
          <span class="input-group-text">Durum</span>
          <select name="status" class="form-control">
            <option value="" selected>- Durum Seçin (Yok)</option>
          </select>
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Tarih</span>
          <input type="date" class="form-control" name="created_at[0]" placeholder="Tarih Başlangıç">
          <input type="date" class="form-control" name="created_at[1]" placeholder="Tarih Bitiş">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Termin</span>
          <input type="date" class="form-control" name="finished_at[0]" placeholder="Termin Başlangıç">
          <input type="date" class="form-control" name="finished_at[1]" placeholder="Termin Bitiş">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Numara</span>
          <input type="number" class="form-control" name="id[0]" placeholder="Numara Başlangıç">
          <input type="number" class="form-control" name="id[1]" placeholder="Numara Bitiş">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Müşteri</span>
          <input type="text" class="form-control" name="user_name" placeholder="Müşteri">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Kullanıcı</span>
          <input type="text" class="form-control" name="auth_name" placeholder="Kullanıcı">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Not</span>
          <input type="text" class="form-control" name="note" placeholder="Not">
        </div>
        <div class="input-group mb-3">
          <span class="input-group-text">Adet</span>
          <input type="number" class="form-control" name="quantity[0]" placeholder="Adet Başlangıç">
          <input type="number" class="form-control" name="quantity[1]" placeholder="Adet Bitiş">
        </div>
        <div class="input-group">
          <span class="input-group-text">Ağırlık</span>
          <input type="number" class="form-control" name="weight[0]" placeholder="Ağırlık Başlangıç">
          <input type="number" class="form-control" name="weight[1]" placeholder="Ağırlık Bitiş">
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