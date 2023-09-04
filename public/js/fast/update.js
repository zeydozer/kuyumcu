let fast = {
  init: function () {
    $.ajax({
      type: 'GET',
      url: '/api/orders/' + ORDER_ID,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      data: {
        fast: true
      },
      beforeSend: function () {
        $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        $('cart-count').html(resp.carts.length)
        $('[name=user]').val(resp.user.name)
        let finished_at = resp.finished_at.split('.')
        $('[name=finished_at]').val(`${finished_at[2]}-${finished_at[1]}-${finished_at[0]}`)
        $('[name=note]').val(resp.note)
        fast.customer(resp.user.name, resp.user.id)
        $('#fast-container').show()
        for (let i = 0; i < PRODUCT_TYPE.length; i++)
          fast.cart.init(PRODUCT_TYPE[i], true)
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
                  `<a href="#" onclick="fast.image('/img/product/${cart.product.photo}'); return false">
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
                  `<a class="btn btn-info" onclick="fast.image('/img/cart/${cart.photo}')">
                    <i class="bi-image"></i>
                  </a>`
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
          $('#detail-tab-pane .' + type + ' tbody').html(rows)
          $('#detail-tab-pane .' + type + ' total-quantity').html(total.quantity.toLocaleString('tr-TR'))
          $('#detail-tab-pane .' + type + ' total-weight').html(total.weight.toLocaleString('tr-TR'))
        }
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        $('.loading').removeClass('show z-1').addClass('z-n1')
      }
    })
  },
  customer: function (name = null, id = null) {
    $.ajax({
      type: 'GET',
      url: '/api/users',
      data: {
        page: 1,
        role: 1,
        name: name
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        $('#user-results').html(spinner).slideDown(150)
        $('[name=user_id]').off('change')
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
        fast.product()
      },
      error: function (resp) {
        $('#user-results').html(resp.responseJSON.message)
      },
      complete: function () {
        let height = $('#info').height() - $('#products .input-group').height()
        $('#products .table-responsive').css('height', `calc(${height}px - ${$('#products .input-group').css('margin-bottom')})`)
        $('[name=user_id]').on('change', function () {
          $('#products [name=product]').val(null)
          fast.product()
        })
        if (id != null)
          $(`[for="user-${id}"]`).trigger('click')
      }
    })
  },
  product: function (name = null) {
    let user_id =
      $('[name=user_id]:checked').length > 0 ?
      $('[name=user_id]:checked').val() :
      USER.id
    $.ajax({
      type: 'GET',
      url: '/api/products',
      data: {
        page: 1,
        order: ['name', 'ASC'],
        name: name,
        user_id: user_id
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        $('#products tbody').html(`<tr><td>${spinner}</td></tr>`).slideDown(150)
      },
      success: function (resp) {
        let products = ``
        if (resp.data.length == 0)
          products += `<tr><td>Ürün bulunamadı.</td></tr>`
        else {
          resp.data.forEach((product, i) => {
            products +=
              `<tr>
                <td>`
              if (product.photo != null) {
                products += 
                  `<a href="#" onclick="fast.image('/img/product/${product.photo}'); return false">
                    ${product.name}
                  </a>`
              } else {
                products +=
                  `${product.name}`
              }
            products +=
                `</td>
                <td>${product.width}</td>
                <td>${product.weight.toLocaleString('tr-TR')}</td>
                <td>`
            let windowControl = false
            if (product.windows != null) {
              product.windows.forEach((window, j) => {
                if (user_id == window.user_id && product.id == window.product_id) {
                    products +=
                      `<a href="#" class="btn btn-warning" onclick="fast.window.delete(${product.id}, this); return false">
                        <i class="bi-window-dash"></i>
                      </a>`
                    windowControl = true
                  }
              })
            } 
            if (!windowControl) {
              products +=
                `<a href="#" href="#" class="btn btn-info" onclick="fast.window.add(${product.id}, this); return false">
                  <i class="bi-window-plus"></i>
                </a>`
            }
            products +=
                `</td>
                <td>
                  <a href="#" class="btn btn-primary" onclick="fast.cart.add(${product.id}, this); return false">
                    <i class="bi-plus"></i>
                  </a>
                </td>
              </tr>`
          })
        }
        $('#products tbody').html(products)
      },
      error: function (resp) {
        $('#products tbody').html(`<tr><td>${resp.responseJSON.message}</td></tr>`)
      }
    })
  },
  cart: {
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
          $('#myTabContent input').off('keyup change')
          $('#cart-tab-pane .' + type).show()
          if (first)
            $('#cart-tab-pane .' + type + ' tbody').html(`<tr><td colspan="${$('#cart-tab-pane .' + type + ' thead:eq(0) th').length}" class="text-start">${spinner}</td></tr>`)
        },
        success: function (resp) {
          let rows = resp.data.length > 0 ? `` : `<tr><td colspan="${$('#cart-tab-pane .' + type + ' thead:eq(0) th').length}" class="text-start">Ürün bulunamadı.</td></tr>`
          resp.data.forEach((cart, i) => {
            rows +=
              `<tr data-id="${cart.id}">
                <th scope="row">${i + 1}</th>
                <td>`
              if (cart.product.photo != null) {
                rows += 
                  `<a href="#" onclick="fast.image('/img/product/${cart.product.photo}'); return false">
                    ${cart.product.name}
                  </a>`
              } else {
                rows +=
                  `${cart.product.name}`
              }
              rows +=
                  `<input type="hidden" name="cart[${cart.id}][product_id]" value="${cart.product.id}">
                </td>
                <td>`
              if (cart.photo != null) {
                rows +=
                  `<a href="#" class="btn btn-info" onclick="fast.image('/img/cart/${cart.photo}'); return false">
                    <i class="bi-image"></i>
                  </a>
                  <div class="form-check mt-1">
                    <input class="form-check-input" name="cart[${cart.id}][photo-del]" type="checkbox" id="photo-del-${cart.id}"
                      onchange="$(this).closest('td').find('[type=file]').slideToggle(150)">
                    <label class="form-check-label" for="photo-del-${cart.id}">
                      Fotoğrafı kaldır.
                    </label>
                  </div>`
              }
              rows +=
                  `<input type="file" accept="image/*" name="cart[${cart.id}][photo]" ${cart.photo != null ? 'class="mt-1"' : ''}>
                </td>
                <td><input type="text" name="cart[${cart.id}][note]" value="${cart.note == null ? '-' : cart.note}"></td>
                <td><input type="number" value="${cart.width}" name="cart[${cart.id}][width]" min="1"></td>`
            for (let j = HEIGHTS[cart.product.type].min; j <= HEIGHTS[cart.product.type].max; j += HEIGHTS[cart.product.type].between) {
              rows +=
                `<td><input type="number" value="${cart.height['height_' + j]}" name="cart[${cart.id}][height][${j}]" min="0"></td>`
            }
              rows +=
                `<td><input type="number" value="${cart.weight.replace('.', '').replace(',', '.')}" name="cart[${cart.id}][weight]" step="0.01" min="0"></td>
                <td>
                  <cart-quantity>${cart.quantity.toLocaleString('tr-TR')}</cart-quantity>
                  <input type="hidden" name="cart[${cart.id}][quantity]" value="${cart.quantity.replace('.', '').replace(',', '.')}">
                </td>
                <td>
                  <cart-weight>${cart.weight_total.toLocaleString('tr-TR')}</cart-weight>
                  <input type="hidden" name="cart[${cart.id}][fast_weight]" value="${cart.weight_total.replace('.', '').replace(',', '.')}">
                </td>
                <td>
                  <a href="#" class="btn btn-danger" onclick="fast.cart.delete(${cart.id}, this); return false">
                    <i class="bi-trash"></i>
                  </a>
                </td>
              </tr>`
          })
          $('#cart-tab-pane .' + type + ' tbody').html(rows)
          $('#cart-tab-pane .' + type + ' total-quantity').html(resp.total.quantity.toLocaleString('tr-TR'))
          $('#cart-tab-pane .' + type + ' total-weight').html(resp.total.weight.toLocaleString('tr-TR'))
          if (resp.data.length == 0)
            $('#cart-tab-pane .' + type).hide()
        },
        error: function (resp) {
          $('#cart-tab-pane .' + type + ' tbody').html(`<tr><td colspan="${$('#cart-tab-pane .' + type + ' thead:eq(0) th').length}" class="text-start">${resp.responseJSON.message}</td></tr>`)
        },
        complete: function () {
          $('#myTabContent input').on('keyup change', function () {
            let cartId = $(this).closest('tr').attr('data-id'),
              weight = 
                $.isNumeric($(this).closest('tr').find('[name*=weight]').val()) ?
                Number($(this).closest('tr').find('[name*=weight]').val()) :
                0,
              quantity = 0
            $(this).closest('tr').find('[name*=height]').each(function () {
              let value =
                $.isNumeric($(this).val()) ?
                Number($(this).val()) :
                0
              quantity += value
            })
            $(this).closest('tr').find('cart-quantity').html(quantity.toLocaleString('tr-TR'))
            $(this).closest('tr').find('[name*=quantity]').val(quantity)
            $(this).closest('tr').find('cart-weight').html((quantity * weight).toLocaleString('tr-TR'))
            $(this).closest('tr').find('[name*=fast_weight]').val((quantity * weight))
            quantity = 0
            weight = 0
            $(this).closest('tbody').find('tr').each(function () {
              quantity += Number($(this).find('cart-quantity').html().replace('.', '').replace(',', '.'))
              weight += Number($(this).find('cart-weight').html().replace('.', '').replace(',', '.'))
            })
            $(this).closest('table').find('total-quantity').html(quantity.toLocaleString('tr-TR'))
            $(this).closest('table').find('total-weight').html(weight.toLocaleString('tr-TR'))
          })
          if (Number($('cart-count').html()) > 0)
            $('[type=submit]').show()
          else
            $('[type=submit]').hide()
        }
      })
    },
    add: function (id, _this) {
      let button = $(_this),
        buttonHtml = button.html(),
        datas = {fast: true},
        product
        $.ajax({
          type: 'GET',
          url: '/api/products/' + id,
          headers: {
            'Authorization': 'Bearer ' + USER.token
          },
          beforeSend: function () {
            button.attr('disabled', true).html(spinner)
          },
          success: function (resp) {
            product = resp
            datas.id = resp.id
            datas.width = resp.width
            datas.weight = resp.weight
            datas.quantity = 0
            datas.weight_total = 0
            datas.height = {}
            for (let i = HEIGHTS[resp.type].min; i <= HEIGHTS[resp.type].max; i += HEIGHTS[resp.type].between)
              datas.height[i] = 0
          },
          error: function (resp) {
            alertCustom('danger', resp.responseJSON.message)
          },
          complete: function () {
            $.ajax({
              type: 'POST',
              url: '/api/carts',
              headers: {
                'Authorization': 'Bearer ' + USER.token
              },
              data: datas,
              success: function (resp) {
                $('cart-count').html(resp.user.carts_count)
                fast.cart.init(product.type)
              },
              error: function (resp) {
                alertCustom('danger', resp.responseJSON.message)
              },
              complete: function () {
                button.removeAttr('disabled').html(buttonHtml)
              }
            })
          }
        })
    },
    delete: function (id, _this) {
      $(`[data-id=${id}] [name*=height]`).val(0)
      $(`[data-id=${id}] [name*=height]`).eq(0).trigger('change')
    }
  },
  window: {
    add: function (id, _this) {
      let user_id =
        $('[name=user_id]:checked').length > 0 ?
        $('[name=user_id]:checked').val() :
        USER.id,
        button = $(_this),
        buttonHtml = button.html(),
        datas = {
          user_id: user_id,
          product_id: id
        }
      $.ajax({
        type: 'POST',
        url: '/api/windows',
        headers: {
          'Authorization': 'Bearer ' + USER.token
        },
        data: datas,
        beforeSend: function () {
          button.attr('disabled', true).html(spinner)
        },
        success: function (resp) {
          button.attr('class', button.attr('class').replace('info', 'warning'))
            .attr('onclick', button.attr('onclick').replace('add', 'delete'))
          buttonHtml = buttonHtml.replace('plus', 'dash')
        },
        error: function (resp) {
          alertCustom('danger', resp.responseJSON.message)
        },
        complete: function () {
          button.removeAttr('disabled').html(buttonHtml)
        }
      })
    },
    delete: function (id, _this) {
      let user_id =
        $('[name=user_id]:checked').length > 0 ?
        $('[name=user_id]:checked').val() :
        USER.id,
        button = $(_this),
        buttonHtml = button.html(),
        datas = {
          user_id: user_id,
          product_id: id,
          _method: 'DELETE'
        }
      $.ajax({
        type: 'POST',
        url: '/api/windows/0',
        headers: {
          'Authorization': 'Bearer ' + USER.token
        },
        data: datas,
        beforeSend: function () {
          button.attr('disabled', true).html(spinner)
        },
        success: function (resp) {
          button.attr('class', button.attr('class').replace('warning', 'info'))
            .attr('onclick', button.attr('onclick').replace('delete', 'add'))
          buttonHtml = buttonHtml.replace('dash', 'plus')
        },
        error: function (resp) {
          alertCustom('danger', resp.responseJSON.message)
        },
        complete: function () {
          button.removeAttr('disabled').html(buttonHtml)
        }
      })
    },
  },
  order: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button.active'),
      buttonHtml = button.html(),
      totalQuantity = 0,
      totalWeight = 0
    $('#cart-tab-pane total-quantity').each(function () {
      totalQuantity += Number($(this).text().replace('.', '').replace(',', '.'))
    })
    datas.append('quantity', totalQuantity)
    $('#cart-tab-pane total-weight').each(function () {
      totalWeight += Number($(this).text().replace('.', '').replace(',', '.'))
    })
    datas.append('weight', totalWeight)
    datas.append('fast', true)
    datas.append('_method', 'PUT')
    datas.append('submit', button.text())
    $.ajax({
      type: 'POST',
      url: '/api/orders/' + ORDER_ID,
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
        $('.alert .toast').on('hidden.bs.toast', function () {
          location.href = '/orders?status='
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
  image: function (path) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $('#cart-image img').attr('src', path)
    $('#cart-image').modal('show')
  },
}
fast.init()
let keyupTimer
$('#info [name=user]').on('keyup', function () {
  clearTimeout(keyupTimer)
  $('#user-results').hide().html(null)
  let userName = $(this).val()
  /* if (userName.length < 3)
    return false */
  keyupTimer = setTimeout(() => {
    fast.customer(userName)
  }, 1000)
})
$('#products [name=product]').on('keyup', function () {
  clearTimeout(keyupTimer)
  $('#products tbody').hide().html(null)
  let productName = $(this).val()
  /* if (productName.length < 3)
    return false */
  keyupTimer = setTimeout(() => {
    fast.product(productName)
  }, 1000)
})
if (Number($('cart-count').html()) > 0)
  $('[type=submit]').show()
$('[type=submit]').on('click', function () {
  $('[type=submit]').removeClass('active')
  $(this).addClass('active')
})