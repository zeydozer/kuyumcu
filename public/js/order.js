let objLength = (obj) => {
  let i = 0
  for (var x in obj) {
    if (obj.hasOwnProperty(x))
      i++
  }
  return i
}
let capitalizeFirstChar = (str) => 
  str.charAt(0).toUpperCase() + str.substring(1)
const SEARCH_FORM = '#order-search'
let options = ``
for (let j = objLength(ORDER_STATUS) - 2; j >= -1; j--)
  options += `<option value="${j}">${capitalizeFirstChar(ORDER_STATUS[j].name)}</option>`
$(SEARCH_FORM + ' [name=status]').append(options)
setSearchParams()
let order = {
  init: function (first = false) {
    $.ajax({
      type: 'GET',
      url: '/api/orders',
      data: searchParams,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        if (USER.role == 0 && USER.admin == 1)
          $('[name=order]').off('change')
        if (first)
          $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        let rows = resp.data.length > 0 ? `` : `<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">Sipariş bulunamadı.</td></tr>`
        resp.data.forEach((order, i) => {
          let buttons = ``
          if (USER.role == 1) {
            if (order.status <= 0) {
              let statusIndex =
                order.status == 0 ? -1 : 0
              buttons +=
                `<button class="btn btn-${ORDER_STATUS[statusIndex].theme}" onclick="order.status(${order.id}, this, ${statusIndex})">
                  <i class="bi-${ORDER_STATUS[statusIndex].icon}"></i>
                </button>\n`
            }
            buttons +=
              `<button class="btn btn-primary" onclick="order.detail(${order.id}, this)">
                <i class="bi-eye"></i>
              </button>`
          } else if (USER.role == 0) {
            if (USER.admin == 1) {
              let statuses = ``
              for (let j = objLength(ORDER_STATUS) - 2; j >= -1; j--) {
                statuses += 
                  `<li onclick="order.status(${order.id}, this, ${j})">
                    <a class="dropdown-item" href="#" onclick="return false">
                      ${capitalizeFirstChar(ORDER_STATUS[j].name)}
                    </a>
                  </li>\n`
              }
              buttons +=
                `<button class="btn btn-primary" onclick="order.detail(${order.id}, this)">
                  <i class="bi-eye"></i>
                </button>
                <div class="btn-group">
                  <button type="button" class="btn btn-info dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi-three-dots"></i>
                  </button>
                  <ul class="dropdown-menu">
                    ${statuses}
                  </ul>
                </div>
                <button class="btn btn-danger" onclick="order.deleteModal(${order.id}, '${order.user.name}')">
                  <i class="bi-trash"></i>
                </button>`
            } else {
              buttons +=
                `<button class="btn btn-primary" onclick="order.detail(${order.id}, this)">
                  <i class="bi-eye"></i>
                </button>`
            }
          }
          rows +=
            `<tr>`
          if (USER.role == 0 && USER.admin == 1) {
            rows +=
              `<td><input name="order" type="checkbox" value="${order.id}"></td>`
          }
          rows +=
              `<th scope="row">${resp.meta.from + i}</th>
              <td>${order.created_at}</td>
              <td>${order.id}</td>
              <td>${order.user.name}</td>
              <td>${order.auth.name}</td>
              <td>${order.finished_at}</td>
              <td>${order.note != null ? order.note : '-'}</td>
              <td class="quantity">${order.quantity}</td>
              <td>${order.weight}</td>
              <td class="text-capitalize">${ORDER_STATUS[order.status].name}</td>
              <td>${buttons}</td>
            </tr>`
        })
        $('tbody').html(rows)
        $('.table-responsive').show()
        pagination(resp.meta)
        $('total-quantity').html(resp.total.quantity.toLocaleString('tr-TR'))
        $('total-weight').html(resp.total.weight.toLocaleString('tr-TR'))
        if (USER.role == 0 && USER.admin == 1) {
          $('[name=order]').on('change', function () {
            if ($('[name=order]:checked').length > 0)
              $('#status').fadeIn(150)
            else
              $('#status').fadeOut(150)
          })
        }
      },
      error: function (resp) {
        // alertCustom('danger', resp.responseJSON.message)
        $('tbody').html(`<tr><td colspan="${$('thead:eq(0) th').length}" class="text-start">${resp.responseJSON.message}</td></tr>`)
        $('.table-responsive').show()
      },
      complete: function () {
        if (first) {
          $('.loading').removeClass('show z-1').addClass('z-n1')
          $('#order-container').show()
        }
        setSort()
      }
    })
  },
  status: function (id, _this, status) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    let button = $(_this),
      buttonHtml = button.html()
    datas = {
      _method: 'PUT',
      status: status
    }
    $.ajax({
      type: 'POST',
      url: '/api/orders/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: datas,
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        order.alert('Sipariş durumu güncellendi.')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  statusCheck: function (status, _this) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    let button = $(_this),
      buttonHtml = button.html(),
      ids = []
    $('[name=order]:checked').each(function () {
      ids.push($(this).val())
    })
    datas = {
      _method: 'PUT',
      status: status,
      ids: ids
    }
    $.ajax({
      type: 'POST',
      url: '/api/orders/0',
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: datas,
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        order.alert('Siparişlerin durumu güncellendi.')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  tab: function (status) {
    $('#order-search [name=status]').val(status)
    $('#order-search form').trigger('submit');
  },
  deleteModal: function (id, userName) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#order-delete order-no').html(id)
    $('#order-delete user-name').html(userName)
    $('#order-delete button').attr('onclick', 'order.delete("' + id + '", this)')
    $('#order-delete').modal('show')
  },
  delete: function (id, _this) {
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/orders/' + id,
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
        $('#order-delete').modal('hide')
        $('#order-delete').on('hidden.bs.modal', order.alert('Sipariş silindi.'))
        setTimeout(() => {
          $('#order-delete').off('hidden.bs.modal')
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
  report: function (type) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    let pageLink = `/reports?type=${type}`
    for (let name in searchParams) {
      if (name != 'page' && searchParams[name] != '')
        pageLink += `&${name}=${searchParams[name]}`
    }
    let anchor = document.createElement('a')
    anchor.href = pageLink
    anchor.target = "_blank"
    anchor.click()
  },
  detail: function (id, _this) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'GET',
      url: '/api/orders/' + id,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        button.attr('disabled', true).html(spinner)
      },
      success: function (resp) {
        $('#order-detail order-no').html(id)
        if (USER.role == 0 && USER.admin == 1 && resp.status != 1) {
          $('#order-detail .edit').attr('href', '/order-fast/' + id).html('Düzenle')
          $('#order-detail .edit').show()
        } else 
          $('#order-detail .edit').hide()
        for (let j = 0; j < PRODUCT_TYPE.length; j++) {
          let rows = ``,
            type = PRODUCT_TYPE[j],
            total = {
              'quantity': 0,
              'weight': 0
            }
          resp.carts.forEach((cart, i) => {
            cart.product.type = cart.product.type.toLocaleLowerCase()
            if (cart.product.type != type)
              return
            rows +=
              `<tr data-id="${cart.id}">
                <th scope="row">${i + 1}</th>
                <td>`
              if (cart.product.photo != null) {
                rows += 
                  `<a href="#" onclick="order.cartImage('/img/product/${cart.product.photo}'); return false">
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
                  `<button class="btn btn-info" onclick="order.cartImage('/img/cart/${cart.photo}')">
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
                `<td>${cart.weight.toLocaleString('tr-TR')}</td>
                <td>${cart.quantity.toLocaleString('tr-TR')}</td>
                <td>${cart.weight_total.toLocaleString('tr-TR')}</td>
              </tr>`
            total.quantity += cart.quantity
            total.weight += cart.weight_total
          })
          $('#' + type + ' tbody').html(rows)
          $('#' + type + ' total-quantity').html(total.quantity.toLocaleString('tr-TR'))
          $('#' + type + ' total-weight').html(total.weight.toLocaleString('tr-TR'))
        }
        $('#order-detail .table-responsive').show()
        $('#order-detail').modal('show')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
        tableMargin()
      }
    })
  },
  cartImage: function (path) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#cart-image img').attr('src', path)
    $('#cart-image').modal('show')
  },
  alert: function (message) {
    alertCustom('success', message)
    $('.alert .toast').on('hidden.bs.toast', order.init())
    $('.alert .toast').off('hidden.bs.toast')
  }
}
order.init(true)
if (USER.role == 0 && USER.admin == 1) {
  $('[name=order-all]').change(function () {
    if ($(this).is(':checked'))
      $('[name=order]:not(:checked)').trigger('click')
    else
      $('[name=order]:checked').trigger('click')
  })
}
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