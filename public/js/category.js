const SEARCH_FORM = '#category-search'
setSearchParams()
let category = {
  init: function (first = false) {
    $.ajax({
      type: 'GET',
      url: '/api/categories',
      data: searchParams,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        if (first)
          $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        let rows = resp.data.length > 0 ? `` : `<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">Kategori bulunamadı.</td></tr>`
        resp.data.forEach((ctg, i) => {
          rows +=
            `<tr>
            <th scope="row">${resp.meta.from + i}</th>
            <td>${ctg.name}</td>
            <td>${ctg.root != null ? ctg.root.name : '-'}</td>
            <td>${ctg.created_at}</td>
            <td>${ctg.updated_at}</td>
            <td>
              <button class="btn btn-info" onclick="category.updateModal(${ctg.id}, this)">
                <i class="bi-pen"></i>
              </button>
              <button class="btn btn-danger" onclick="category.deleteModal(${ctg.id}, '${ctg.name}')">
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
          $('#category-container').show()
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
      url: '/api/categories',
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
        category.alert('Kategori eklendi.')
        $('#category-new').modal('hide')
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
    $('#category-delete span').html(name)
    $('#category-delete button').attr('onclick', 'category.delete("' + id + '", this)')
    $('#category-delete').modal('show')
  },
  delete: function (id, _this) {
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/categories/' + id,
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
        $('#category-delete').modal('hide')
        $('#category-delete').on('hidden.bs.modal', category.alert('Kategori silindi.'))
        setTimeout(() => {
          $('#category-delete').off('hidden.bs.modal')
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
    $('#category-update').find('#root-results').hide().html(null)
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'GET',
      url: '/api/categories/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        $('#category-update [name="id"]').val(resp.id)
        $('#category-update [name="name"]').val(resp.name)
        if (resp.root != null) {
          $('#category-update [name="root_id_default"]').val(resp.root.id)
          $('#category-update [name="root"]').val(resp.root.name).trigger('keyup')
          /* let rootRadio =
            `<div class="form-check">
              <input class="form-check-input" type="radio" name="root_id" value="${resp.root.id}" id="root-${resp.root.id}" checked>
              <label class="form-check-label" for="root-${resp.root.id}">${resp.root.name}</label>
            </div>`
          $('#category-update #root-results').html(rootRadio).slideDown(150) */
        }
        $('#category-update').modal('show')
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
      url: '/api/categories/' + datas.get('id'),
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
        category.alert('Kategori güncellendi.')
        $('#category-update').modal('hide')
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
    $('.alert .toast').on('hidden.bs.toast', category.init())
    $('.alert .toast').off('hidden.bs.toast')
  }
}
category.init(true)
$('#category-new').on('show.bs.modal', function () {
  bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
  $(this).find('input, select').val(null)
  $(this).find('#root-results').hide().html(null)
})
let keyupTimer
$('.modal [name=root]').on('keyup', function () {
  clearTimeout(keyupTimer)
  let parentModal = $(this).closest('.modal')
  parentModal.find('#root-results').hide().html(null)
  let rootName = $(this).val()
  if (rootName.length < 3)
    return false
  keyupTimer = setTimeout(() => {
    $.ajax({
      type: 'GET',
      url: '/api/categories',
      data: {
        name: rootName
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        parentModal.find('#root-results').html(spinner).slideDown(150)
      },
      success: function (resp) {
        let roots = ``
        if (resp.data.length == 0)
          roots += `Kategori bulunamadı.`
        else {
          let root_id
          if ($('[name=root_id_default]').length > 0)
            root_id = $('[name=root_id_default]').val()
          resp.data.forEach((root, i) => {
            roots +=
              `<div class="form-check">
                <input class="form-check-input" type="radio" name="root_id" value="${root.id}" id="root-${root.id}"
                  ${root.id == root_id ? 'checked' : ''}>
                <label class="form-check-label" for="root-${root.id}">${root.name}</label>
              </div>`
          })
        }
        parentModal.find('#root-results').html(roots)
      },
      error: function (resp) {
        parentModal.find('#root-results').html(resp.responseJSON.message)
      }
    })
  }, 1000)
})