const SEARCH_FORM = '#user-search'
setSearchParams()
let user = {
  init: function (first = false) {
    $.ajax({
      type: 'GET',
      url: '/api/users',
      data: searchParams,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        if (first)        
          $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        let rows = resp.data.length > 0 ? `` : `<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">Kullanıcı bulunamadı.</td></tr>`
        resp.data.forEach((user, i) => {
          rows +=
            `<tr>
            <th scope="row">${resp.meta.from + i}</th>
            <td>${user.name}</td>
            <td>${user.role == 0 ? 'Personel' : 'Müşteri'}</td>
            <td>${user.mail}</td>
            <td>${user.phone != null ? user.phone : '-'}</td>
            <td>${user.address != null ? user.address : '-'}</td>
            <td>${user.created_at}</td>
            <td>${user.updated_at}</td>
            <td>
              <button class="btn btn-info" onclick="user.updateModal(${user.id}, this)">
                <i class="bi-pen"></i>
              </button>
              <button class="btn btn-danger" onclick="user.deleteModal(${user.id}, '${user.name}')">
                <i class="bi-trash"></i>
              </button>
            </td>
          </tr>`
        })
        $('tbody').html(rows)
        $('.table-responsive').show()
        pagination(resp.meta)
      },
      error: function (resp) {
        // alertCustom('danger', resp.responseJSON.message)
        $('tbody').html(`<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">${resp.responseJSON.message}</td></tr>`)
        $('.table-responsive').show()
      },
      complete: function () {
        if (first) {
          $('.loading').removeClass('show z-1').addClass('z-n1')
          $('#user-container').show()
        }
        setSort()
      }
    })
  },
  new: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button'),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/users',
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: datas,
      contentType: false,
      processData: false,
      cache: false,
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        user.alert('Kullanıcı eklendi.')
        $('#user-new').modal('hide')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  deleteModal: function (id, name) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#user-delete span').html(name)
    $('#user-delete button').attr('onclick', 'user.delete("' + id + '", this)')
    $('#user-delete').modal('show')
  },
  delete: function (id, _this) {
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/users/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: {
        '_method': 'DELETE'
      },
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        $('#user-delete').modal('hide')
        $('#user-delete').on('hidden.bs.modal', user.alert('Kullanıcı silindi.'))
        setTimeout(() => {
          $('#user-delete').off('hidden.bs.modal')
        }, 100)
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  updateModal: function (id, _this) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    if (USER.admin == 1) {
      $('#user-update').find('.admin').hide()
      if ($('#update-admin').is(':checked'))
        $('[for=update-admin]').trigger('click')
    }
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'GET',
      url: '/api/users/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        $('#user-update [name="id"]').val(resp.id)
        $('#user-update [name="role"]').val(resp.role)
        if (resp.role == 0)
          $('#user-update').find('.admin').slideDown(150)
        if (resp.admin == 1)
          $('[for=update-admin]').trigger('click')
        $('#user-update [name="name"]').val(resp.name)
        $('#user-update [name="mail"]').val(resp.mail)
        $('#user-update [name="phone"]').val(resp.phone)
        $('#user-update [name="address"]').val(resp.address)
        $('#user-update').modal('show')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  update: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button'),
      buttonHtml = button.html()
    datas.append('_method', 'PUT')
    $.ajax({
      type: 'POST',
      url: '/api/users/' + datas.get('id'),
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: datas,
      contentType: false,
      processData: false,
      cache: false,
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        user.alert('Kullanıcı güncellendi.')
        $('#user-update').modal('hide')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  alert: function (message) {
    alertCustom('success', message)
    $('.alert .toast').on('hidden.bs.toast', user.init())
    $('.alert .toast').off('hidden.bs.toast')
  }
}
user.init(true)
$('#user-new').on('show.bs.modal', function () {
  bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
  $(this).find('input, select').val(null)
  if (USER.admin == 0)
    $(this).find('[name=role]').val(1)
  else {
    $(this).find('.admin').hide()
    if ($('#new-admin').is(':checked'))
      $('[for=new-admin]').trigger('click')
  }
})
if (USER.admin == 1) {
  $('[name=role]').change(function () {
    if ($(this).val() == 0)
      $(this).closest('.modal').find('.admin').slideDown(150)
    else {
      $(this).closest('.modal').find('.admin').slideUp(150)
      if ($(this).closest('.modal').find('.admin input').is(':checked'))
        $(this).closest('.modal').find('.admin label').trigger('click')
    }
  })
}