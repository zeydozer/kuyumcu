let cart = {
  init: function (type, first = false) {
    $.ajax({
      type: 'GET',
      url: '/api/carts',
      data: {
        type: type
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        if (first)
          $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        let rows = resp.data.length > 0 ? `` : `<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">Ürün bulunamadı.</td></tr>`
        resp.data.forEach((cart, i) => {
          rows +=
            `<tr data-id="${cart.id}">
              <th scope="row">${i + 1}</th>
              <td>`
            if (cart.product.photo != null) {
              rows += 
                `<a href="#" onclick="cart.image('/img/product/${cart.product.photo}'); return false">
                  ${cart.product.name}
                </a>`
            } else {
              rows +=
                `${cart.product.name}`
            }
            rows +=
              `</td>
              <td>`
            if (cart.photo != null) {
              rows +=
                `<button class="btn btn-info" onclick="cart.image('/img/cart/${cart.photo}')">
                  <i class="bi-image"></i>
                </button>`
            } else
              rows += `-`
            rows +=
              `</td>
              <td>${cart.note == null ? '-' : cart.note}</td>
              <td>${cart.width}</td>`
          for (let j = HEIGHTS[cart.product.type].min; j <= HEIGHTS[cart.product.type].max; j += HEIGHTS[cart.product.type].between) {
            rows +=
              `<td>${cart.height['height_' + j]}</td>`
          }
            rows +=
              `<td>${cart.weight}</td>
              <td>${cart.quantity.toLocaleString('tr-TR')}</td>
              <td>${cart.weight_total.toLocaleString('tr-TR')}</td>
              <td>
                <button class="btn btn-info" onclick="cart.updateModal(${cart.id}, this)">
                  <i class="bi-pen"></i>
                </button>
                <button class="btn btn-danger" onclick="cart.deleteModal(${cart.id}, '${cart.product.name}')">
                  <i class="bi-trash"></i>
                </button>
              </td>
            </tr>`
        })
        $('#' + type + ' tbody').html(rows)
        $('#' + type + ' total-quantity').html(resp.total.quantity.toLocaleString('tr-TR'))
        $('#' + type + ' total-weight').html(resp.total.weight.toLocaleString('tr-TR'))
        $('#' + type).show()
        /* if (resp.data.length > 0)
          $('#' + type).show()
        else
          $('#' + type).hide() */
        $('.table-responsive').show()
      },
      error: function (resp) {
        // alertCustom('danger', resp.responseJSON.message)
        $('#' + type + ' tbody').html(`<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">${resp.responseJSON.message}</td></tr>`)
        $('#' + type).show()
        $('.table-responsive').show()
      },
      complete: function () {
        if (first) {
          $('.loading').removeClass('show z-1').addClass('z-n1')
          $('#cart-container').show()
        }
        tableMargin()
      }
    })
  },
  image: function (path) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#cart-image img').attr('src', path)
    $('#cart-image').modal('show')
  },
  deleteModal: function (id, name) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#cart-delete span').html(name)
    $('#cart-delete button').attr('onclick', 'cart.delete("' + id + '", this)')
    $('#cart-delete').modal('show')
  },
  delete: function (id, _this) {
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/carts/' + id,
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
        $('#cart-delete').modal('hide')
        $('#cart-delete').on('hidden.bs.modal', cart.alert('Ürün sepetten silindi.', resp.product.type))
        $('cart-count').html(resp.user.carts_count)
        setTimeout(() => {
          $('#cart-delete').off('hidden.bs.modal')
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
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'GET',
      url: '/api/carts/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        $('#cart-update .modal-title').html(resp.product.name)
        $('#cart-update [name="id"]').val(resp.id)
        $('#cart-update [name="product_id"]').val(resp.product.id)
        $('#cart-update [name="width"]').val(resp.width)
        $('#cart-update [name="weight"]').val(resp.weight.replace('.', '').replace(',', '.'))
        $('#cart-update [name="note"]').val(resp.note)
        let heightRow = ``
        for (let i = HEIGHTS[resp.product.type].min; i <= HEIGHTS[resp.product.type].max; i += HEIGHTS[resp.product.type].between) {
          heightRow +=
            `<div class="col-4 col-lg-3">
              <div class="input-group">
                <span class="input-group-text">${i}</span>
                <input type="number" class="form-control" name="height[${i}]" placeholder="Boy" value="${resp.height['height_' + i]}" required>
              </div>
            </div>`
        }
        $('#cart-update #heights').html(heightRow)
        $('cart-quantity').html(resp.quantity.toLocaleString('tr-TR'))
        $('cart-weight').html(resp.weight_total.toLocaleString('tr-TR'))
        if (resp.product.empty == 0)
          $('#photo, .form-check').hide()
        else {
          $('#photo').show()
          if (resp.photo == null)
            $('.form-check').closest('div').hide()
          else 
            $('.form-check').closest('div').show()
        }
        $('#cart-update').modal('show')
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
    datas.append('quantity', Number($('cart-quantity').html().replace('.', '')))
    datas.append('weight_total', Number($('cart-weight').html().replace('.', '').replace(',', '.')))
    $.ajax({
      type: 'POST',
      url: '/api/carts/' + datas.get('id'),
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
        cart.alert('Sepetteki ürün güncellendi.', resp.product.type)
        $('#cart-update').modal('hide')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  order: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button'),
      buttonHtml = button.html(),
      totalQuantity = 0,
      totalWeight = 0
    $('total-quantity').each(function () {
      totalQuantity += Number($(this).text().replace('.', ''))
    })
    datas.append('quantity', totalQuantity)
    $('total-weight').each(function () {
      totalWeight += Number($(this).text().replace('.', '').replace(',', '.'))
    })
    datas.append('weight', totalWeight)
    $.ajax({
      type: 'POST',
      url: '/api/orders',
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
        alertCustom('success', 'Sipariş kaydedildi.')
        $('#cart-order').modal('hide')
        $('.alert .toast').on('hidden.bs.toast', function () {
          location.href = '/orders?status=0'
        })
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  alert: function (message, type) {
    alertCustom('success', message)
    $('.alert .toast').on('hidden.bs.toast', cart.init(type))
    $('.alert .toast').off('hidden.bs.toast')
  }
}
for (let i = 0; i < PRODUCT_TYPE.length; i++)
  cart.init(PRODUCT_TYPE[i], true)
$('#cart-update').on('show.bs.modal', function () {
  if ($('[name="photo-del"]').is(':checked'))
    $('[for="photo-del"]').trigger('click')
  $(this).find('[name=photo]').val(null)
  $(this).find('[name^=height], [name=weight]').on('keyup change', function () {
    let quantity = 0
    $('#cart-update [name^=height]').each(function () {
      quantity += Number($(this).val())
    })
    let weight = quantity * $('#cart-update [name=weight]').val()
    $('cart-quantity').html(quantity.toLocaleString('tr-TR'))
    $('cart-weight').html(weight.toLocaleString('tr-TR'))
  })
}).on('hide.bs.modal', function () {
  $(this).find('[name^=height], [name=weight]').off('keyup change')
}).on('shown.bs.modal', function () {
  $(this).find('[name^=height]').eq(0).focus()
})
let keyupTimer
$('#cart-order [name=user]').on('keyup', function () {
  clearTimeout(keyupTimer)
  $('#user-results').hide().html(null)
  let userName = $(this).val()
  if (userName.length < 3)
    return false
  keyupTimer = setTimeout(() => {
    $.ajax({
      type: 'GET',
      url: '/api/users',
      data: {
        name: userName,
        role: 1
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        $('#user-results').html(spinner).slideDown(150)
      },
      success: function (resp) {
        let users = ``
        if (resp.data.length == 0)
          users += `Müşteri bulunamadı.`
        else {
          resp.data.forEach((user, i) => {
            users +=
              `<div class="form-check">
                <input class="form-check-input" type="radio" name="user_id" value="${user.id}" id="user-${user.id}">
                <label class="form-check-label" for="user-${user.id}">${user.name}</label>
              </div>`
          })
        }
        $('#user-results').html(users)
      },
      error: function (resp) {
        $('#user-results').html(resp.responseJSON.message)
      }
    })
  }, 1000)
})
function tableMargin() {
  setTimeout(() => {
    let j
    $('.table').each(function (i) {
      $(this).removeClass('mb-0')
      if ($(this).css('display') != 'none')
        j = i
    })
    $('.table').eq(j).addClass('mb-0')
  }, 2000)
}