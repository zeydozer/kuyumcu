<div class="modal fade ord-modal" id="order-search" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <form class="modal-content">
      <div class="modal-header">
        <h1 class="modal-title">Sipariş Ara</h1>
        <a class="btn-close" data-bs-dismiss="modal" aria-label="Kapat"></a>
      </div>
      <div class="modal-body">
        <input type="hidden" name="page" id="current-page">
        <input type="hidden" name="sort" id="current-sort">
        <div class="input-group">
          <span class="input-group-text">Durum</span>
          <select name="status" class="form-control">
            <option value="" selected>- Durum Seçin (Yok)</option>
          </select>
        </div>
        <div class="input-group">
          <span class="input-group-text">Tarih</span>
          <input type="date" class="form-control" name="created_at[0]">
          <input type="date" class="form-control" name="created_at[1]">
        </div>
        <div class="input-group">
          <span class="input-group-text">Termin</span>
          <input type="date" class="form-control" name="finished_at[0]">
          <input type="date" class="form-control" name="finished_at[1]">
        </div>
        <div class="input-group">
          <span class="input-group-text">Numara</span>
          <input type="number" class="form-control" name="id[0]" placeholder="Başlangıç">
          <input type="number" class="form-control" name="id[1]" placeholder="Bitiş">
        </div>
        <div class="input-group">
          <span class="input-group-text">Müşteri</span>
          <input type="text" class="form-control" name="user_name" placeholder="Müşteri adı">
        </div>
        <div class="input-group">
          <span class="input-group-text">Kullanıcı</span>
          <input type="text" class="form-control" name="auth_name" placeholder="Kullanıcı adı">
        </div>
        <div class="input-group">
          <span class="input-group-text">Not</span>
          <input type="text" class="form-control" name="note" placeholder="Sipariş notu">
        </div>
        <div class="input-group">
          <span class="input-group-text">Adet</span>
          <input type="number" class="form-control" name="quantity[0]" placeholder="Başlangıç">
          <input type="number" class="form-control" name="quantity[1]" placeholder="Bitiş">
        </div>
        <div class="input-group">
          <span class="input-group-text">Ağırlık</span>
          <input type="number" class="form-control" name="weight[0]" placeholder="Başlangıç">
          <input type="number" class="form-control" name="weight[1]" placeholder="Bitiş">
        </div>
      </div>
      <div class="modal-footer">
        <a class="btn btn-secondary" data-bs-dismiss="modal">Vazgeç</a>
        <input type="reset" class="btn btn-info" value="Sıfırla">
        <button type="submit" class="btn btn-primary">Ara</button>
      </div>
    </form>
  </div>
</div>
