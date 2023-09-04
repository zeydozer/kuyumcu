const SEARCH_FORM = '#product-search'
setSearchParams()
let product = {
  init: function (first = false) {
    $.ajax({
      type: 'GET',
      url: '/api/products',
      data: searchParams,
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        if (first)
          $('.loading').removeClass('z-n1').addClass('show z-1')
      },
      success: function (resp) {
        let cols = 
          resp.data.length > 0 ? 
          `` : 
          `<div class="col">Ürün bulunamadı.</div>`
        resp.data.forEach((product, i) => {
          cols +=
            `<div class="col-xl-2 col-lg-3 col-md-4">
              <div class="card w-100">
                <img src="/img/${product.photo != null ? 'product/' + product.photo : 'no-photo.svg'}" class="card-img-top object-fit-contain bg-light" height="200">
                <div class="card-body">
                  <h5 class="card-title text-truncate">${product.name}</h5>
                  <p class="card-text text-truncate">${product.width}mm - ${product.weight.toLocaleString('tr-TR')}gr</p>
                  <button href="#" class="btn btn-primary" onclick="product.cartModal(${product.id}, this)">
                    <i class="bi-cart"></i>
                  </button>\n`
                  if (USER.role == 0) {
                    cols +=
                    `<button href="#" class="btn btn-info" onclick="product.updateModal(${product.id}, this)">
                      <i class="bi-pen"></i>
                    </button>
                    <button href="#" class="btn btn-danger" onclick="product.deleteModal(${product.id}, '${product.name}')">
                      <i class="bi-trash"></i>
                    </button>`
                  } else if (USER.role == 1) {
                    let windowControl = false
                    if (product.windows != null) {
                      product.windows.forEach((window, j) => {
                        if (USER.id == window.user_id && product.id == window.product_id) {
                            cols +=
                              `<button href="#" class="btn btn-warning" onclick="product.window.delete(${product.id}, this)">
                                <i class="bi-window-dash"></i>
                              </button>`
                            windowControl = true
                          }
                      })
                    } 
                    if (!windowControl) {
                      cols +=
                        `<button href="#" class="btn btn-success" onclick="product.window.add(${product.id}, this)">
                          <i class="bi-window-plus"></i>
                        </button>`
                    }
                  }
                cols +=
                `</div>
              </div>
            </div>`
        })
        $('#products').html(cols)
        pagination(resp)
      },
      error: function (resp) {
        $('#products').html(`<div class="col">${resp.responseJSON.message}</div>`)
      },
      complete: function () {
        if (first) {
          $('.loading').removeClass('show z-1').addClass('z-n1')
          $('#product-container').show()
        }
      }
    })
  },
  category: function () {
    $.ajax({
      type: 'GET',
      url: '/api/categories',
      data: {
        root_id : 
          typeof(searchParams.ctg) != 'undefined' ? 
          searchParams.ctg : 
          null,
        product_count: true
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      success: function (resp) {
        let rows
        if (resp.data.length > 0) {
          rows = `<ul class="list-group list-group-flush">`
          if (resp.data[0].root != null) {
            rows +=
              `<li class="list-group-item d-flex justify-content-between align-items-center active" aria-current="true">
                <a href="/products?ctg=${resp.data[0].root.id}">${resp.data[0].root.name}</a>
              </li>`
          }
        } else
          rows = `<span>Kategori bulunamadı.</span>`
        resp.data.forEach((ctg, i) => {
          if (ctg.product_count == undefined)
            ctg.product_count = 0
          rows +=
            `<li class="list-group-item d-flex justify-content-between align-items-center">
              <a href="/products?ctg=${ctg.id}">${ctg.name}</a>
              <span class="badge bg-success rounded-pill">${ctg.product_count}</span>
            </li>`
        })
        if (resp.data.length > 0)
          rows += `</ul>`
        $('#categories').html(rows)
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      }
    })
  },
  new: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button'),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/products',
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
        product.alert('Ürün eklendi.')
        $('#product-new').modal('hide')
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
    $('#product-update').find('#root-results').hide().html(null)
    let button = $(_this),
      buttonHtml = button.html()
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
        $('#product-update [name="id"]').val(resp.id)
        $('#product-update [name="name"]').val(resp.name)
        if (resp.category != null) {
          $('#product-update [name="ctg_id_default"]').val(resp.category.id)
          $('#product-update [name="ctg"]').val(resp.category.name).trigger('keyup')
          /* let ctgRadio =
            `<div class="form-check">
              <input class="form-check-input" type="radio" name="ctg_id" value="${resp.category.id}" id="ctg-${resp.category.id}" checked>
              <label class="form-check-label" for="ctg-${resp.category.id}">${resp.category.name}</label>
            </div>`
          $('#product-update #ctg-results').html(ctgRadio).slideDown(150) */
        }
        if (resp.photo == null)
          $('[name="photo-del"]').closest('div').hide()
        else 
          $('[name="photo-del"]').closest('div').show()
        $('#product-update [name="width"]').val(resp.width)
        $('#product-update [name="weight"]').val(resp.weight)
        $('#product-update [name="between"]').val(resp.between)
        if ((resp.empty == 1 && !$('#update-empty').is(':checked'))
          || (resp.empty == 0 && $('#update-empty').is(':checked')))
          $('[for=update-empty]').trigger('click')
        $('#product-update').modal('show')
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
      url: '/api/products/' + datas.get('id'),
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
        product.alert('Ürün güncellendi.')
        $('#product-update').modal('hide')
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
    $('#product-delete span').html(name)
    $('#product-delete button').attr('onclick', 'product.delete("' + id + '", this)')
    $('#product-delete').modal('show')
  },
  delete: function (id, _this) {
    let button = $(_this),
      buttonHtml = button.html()
    $.ajax({
      type: 'POST',
      url: '/api/products/' + id,
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
        $('#product-delete').modal('hide')
        $('#product-delete').on('hidden.bs.modal', product.alert('Ürün silindi.'))
        setTimeout(() => {
          $('#product-delete').off('hidden.bs.modal')
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
  cartModal: function (id, _this) {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    let button = $(_this),
      buttonHtml = button.html()
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
        $('#product-cart .modal-title').html(resp.name)
        $('#product-cart [name="id"]').val(resp.id)
        $('#product-cart [name="width"]').val(resp.width)
        $('#product-cart [name="weight"]').val(resp.weight)
        let heightRow = ``
        for (let i = HEIGHTS[resp.type].min; i <= HEIGHTS[resp.type].max; i += HEIGHTS[resp.type].between) {
          heightRow +=
            `<div class="col-4 col-lg-3">
              <div class="input-group">
                <span class="input-group-text">${i}</span>
                <input type="number" class="form-control" name="height[${i}]" placeholder="Boy" value="0" required>
              </div>
            </div>`
        }
        $('#product-cart #heights').html(heightRow)
        if (resp.empty == 0)
          $('#product-cart [name=photo]').closest('.input-group').hide()
        else 
          $('#product-cart [name=photo]').closest('.input-group').show()
        $('#product-cart').modal('show')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  cart: function (form) {
    let datas = new FormData(form),
      button = $(form).find('button'),
      buttonHtml = button.html()
    datas.append('quantity', Number($('cart-quantity').html().replace('.', '')))
    datas.append('weight_total', Number($('cart-weight').html().replace('.', '').replace(',', '.')))
    $.ajax({
      type: 'POST',
      url: '/api/carts',
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
        $('cart-count').html(resp.user.carts_count)
        product.alert('Ürün sepete eklendi.')
        $('#product-cart').modal('hide')
      },
      error: function (resp) {
        alertCustom('danger', resp.responseJSON.message)
      },
      complete: function () {
        button.removeAttr('disabled').html(buttonHtml)
      }
    })
  },
  window: {
    add: function (id, _this) {
      bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
      let button = $(_this),
        buttonHtml = button.html(),
        datas = {
          user_id: USER.id,
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
          button.attr('class', button.attr('class').replace('success', 'warning'))
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
      bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
      let button = $(_this),
        buttonHtml = button.html(),
        datas = {
          user_id: USER.id,
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
          button.attr('class', button.attr('class').replace('warning', 'success'))
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
  alert: function (message) {
    alertCustom('success', message)
    let toastOn = function () {
      product.init()
      product.category()
    }
    $('.alert .toast').on('hidden.bs.toast', toastOn())
    $('.alert .toast').off('hidden.bs.toast')
  }
}
product.init(true)
product.category()
if (USER.role == 0) {
  $('#product-new').on('show.bs.modal', function () {
    bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
    $(this).find('input, select').val(null)
    if ($('#new-empty').is(':checked'))
      $('[for=new-empty]').trigger('click')
    $(this).find('#ctg-results').hide().html(null)
  })
  $('#product-update').on('show.bs.modal', function () {
    if ($('[name="photo-del"]').is(':checked'))
      $('[for="photo-del"]').trigger('click')
    $(this).find('[name=photo]').val(null)
  })
}
$('#product-search select').change(function () {
  $(this).closest('form').trigger('submit')
})
$('#product-cart').on('show.bs.modal', function () {
  bootstrap.Toast.getOrCreateInstance($('.alert .toast')).hide()
  $(this).find('[name=note], [name=photo]').val(null)
  $('cart-quantity, cart-weight').html(0)
  $(this).find('[name^=height], [name=weight]').on('keyup change', function () {
    let quantity = 0
    $('#product-cart [name^=height]').each(function () {
      quantity += Number($(this).val())
    })
    let weight = quantity * $('#product-cart [name=weight]').val()
    $('cart-quantity').html(quantity.toLocaleString('tr-TR'))
    $('cart-weight').html(weight.toLocaleString('tr-TR'))
  })
}).on('hide.bs.modal', function () {
  $(this).find('[name^=height], [name=weight]').off('keyup change')
}).on('shown.bs.modal', function () {
  $(this).find('[name^=height]').eq(0).focus()
})
let keyupTimer
$('.modal [name=ctg]').on('keyup', function () {
  clearTimeout(keyupTimer)
  let parentModal = $(this).closest('.modal')
  parentModal.find('#ctg-results').hide().html(null)
  let ctgName = $(this).val()
  if (ctgName.length < 3)
    return false
  keyupTimer = setTimeout(() => {
    $.ajax({
      type: 'GET',
      url: '/api/categories',
      data: {
        name: ctgName
      },
      headers: {
        'Authorization': 'Bearer ' + USER.token
      },
      beforeSend: function () {
        parentModal.find('#ctg-results').html(spinner).slideDown(150)
      },
      success: function (resp) {
        let ctgs = ``
        if (resp.data.length == 0)
          ctgs += `Kategori bulunamadı.`
        else {
          let ctg_id
          if ($('[name=ctg_id_default]').length > 0)
            ctg_id = $('[name=ctg_id_default]').val()
          resp.data.forEach((ctg, i) => {
            ctgs +=
              `<div class="form-check">
                <input class="form-check-input" type="radio" name="ctg_id" value="${ctg.id}" id="ctg-${ctg.id}"
                  ${ctg.id == ctg_id ? 'checked' : ''}>
                <label class="form-check-label" for="ctg-${ctg.id}">${ctg.name}</label>
              </div>`
          })
        }
        parentModal.find('#ctg-results').html(ctgs)
      },
      error: function (resp) {
        parentModal.find('#ctg-results').html(resp.responseJSON.message)
      }
    })
  }, 1000)
})