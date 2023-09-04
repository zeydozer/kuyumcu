@extends('main')

@section('title', 'Rapor')

@php $types = config("const.reportType") @endphp
@section('content')
<table style="display: none" align="center" class="mb-3">
  <thead>
    <tr>
      <th><b>{{ $types[request('type')]['title'] }} <only></only> Raporu</b></th>
      <th>Adet: <total-quantity></total-quantity></th>
      <th>Ağırlık: <total-weight></total-weight></th>
    </tr>
  </thead>
</table>
<table style="display: none" align="center">
  <tbody></tbody>
</table>
@endsection

@section('css')
<style>
  table td,
  table th {
    padding: 2px 10px;
    font-size: 8pt;
    border: 1px solid #000;
  }
  .weight {
    background-color: #000;
    color: #fff;
  }
  .total {
    background-color: #333;
    color: #fff;
  }
  .total:not(:last-child) {
    border-bottom: 1rem solid #fff;
  }
</style>
@endsection

@section('js')
<script>
  $('nav, div').not('.loading, .spinner-border').remove()
  $('html').attr('data-bs-theme', 'light')
  let searchParamss = Object.fromEntries(new URLSearchParams(location.search))
  $.ajax({
    type: 'GET',
    url: '/api/reports',
    data: searchParamss,
    headers: {
      'Authorization': 'Bearer ' + USER.token
    },
    beforeSend: function () {      
      $('.loading').removeClass('z-n1').addClass('show z-1')
      if (searchParamss['only'] == 'special')
        $('only').html('Özel')
      else if (searchParamss['only'] == 'special-out')
        $('only').html('Özelsiz')
    },
    success: function (resp) {
      let rows = 
        resp.data.length > 0 ?
        `` : 
        `<tr><td>Sipariş bulunamadı.</td></tr>`
      if (searchParamss['type'] == 'product') {
        $.each(resp.report, (width, types) => {
          $.each(types, (type, products) => {
            let total = {
              'quantity': 0,
              'weight': 0,
              'height': []
            }
            rows += 
              `<tr class="weight">
                <th>Genişlik: ${width}</th>`
            for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
              rows += 
                `<th>${i}</th>`
              total['height'][i] = 0
            }
            rows += 
              ` <th>Ağırlık</th>
                <th>Adet</th>
                <th>T. Ağırlık</th>
                <th>Pay</th>
              <tr>`
            $.each(products, (id, weights) => {
              $.each(weights, (weight, carts) => {
                if (searchParamss['only'] == 'special' 
                  && carts[0].product.weight == weight)
                  return
                if (searchParamss['only'] == 'special-out' 
                  && carts[0].product.weight != weight)
                  return
                let productTotal = {
                  'quantity': 0,
                  'weight': 0,
                  'height': []
                }
                for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between)
                  productTotal['height'][i] = 0
            rows += 
              `<tr class="${carts[0].product.weight != weight ? 'text-danger' : ''}">
                <td>${carts[0].product.name}</td>`
                $.each(carts, (i, cart) => {
                  productTotal['quantity'] += Number(cart.quantity)
                  productTotal['weight'] += Number(cart.weight_total)
                  for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between)
                    productTotal['height'][i] += Number(cart.height['height_' + i])
                })
            for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
              rows += 
                `<td>${productTotal['height'][i].toLocaleString('tr-TR')}</td>`
              total['height'][i] += productTotal['height'][i]
            }
            rows += 
              ` <td>${Number(weight).toLocaleString('tr-TR')}</td>
                <td>${productTotal['quantity'].toLocaleString('tr-TR')}</td>
                <td>${productTotal['weight'].toLocaleString('tr-TR')}</td>
                <td>${carts[0].product.between.toLocaleString('tr-TR')}</td>
              </tr>`
                total['quantity'] += productTotal['quantity']
                total['weight'] += productTotal['weight']
              })
            })
            rows += 
              `<tr class="total">
                <th>Toplam</th>`
            for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
              rows += 
                `<th>${total['height'][i].toLocaleString('tr-TR')}</th>`
            }
            rows += 
                `<th></th>
                <th>${total['quantity'].toLocaleString('tr-TR')}</th>
                <th>${total['weight'].toLocaleString('tr-TR')}</th>
                <th></th>
              </tr>`
          })
        })
      } else if (searchParamss['type'] == 'customer') {
        $.each(resp.report, (userId, names) => {
          $.each(names, (name, types) => {
            $.each(types, (type, products) => {
              let total = {
                'quantity': 0,
                'weight': 0,
                'height': []
              }
              rows += 
                `<tr class="weight">
                  <th>${name}</th>`
              for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
                rows += 
                  `<th>${i}</th>`
                total['height'][i] = 0
              }
              rows += 
                ` <th>Birim</th>
                  <th>Adet</th>
                  <th>Ağırlık</th>
                  <th>Pay</th>
                <tr>`
              $.each(products, (id, weights) => {
                $.each(weights, (weight, widths) => {
                  $.each(widths, (width, carts) => {
                    if (searchParamss['only'] == 'special' 
                      && carts[0].product.weight == weight)
                      return
                    if (searchParamss['only'] == 'special-out' 
                      && carts[0].product.weight != weight)
                      return
                    let productTotal = {
                      'quantity': 0,
                      'weight': 0,
                      'height': []
                    }
                    for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between)
                      productTotal['height'][i] = 0
              rows += 
                `<tr class="${carts[0].product.weight != weight ? 'text-danger' : ''}">
                  <td>${carts[0].product.name}</td>`
                  $.each(carts, (i, cart) => {
                    productTotal['quantity'] += Number(cart.quantity)
                    productTotal['weight'] += Number(cart.weight_total)
                    for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between)
                      productTotal['height'][i] += Number(cart.height['height_' + i])
                  })
              for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
                rows += 
                  `<td>${productTotal['height'][i].toLocaleString('tr-TR')}</td>`
                total['height'][i] += productTotal['height'][i]
              }
              rows += 
                ` <td>${Number(weight).toLocaleString('tr-TR')}</td>
                  <td>${productTotal['quantity'].toLocaleString('tr-TR')}</td>
                  <td>${productTotal['weight'].toLocaleString('tr-TR')}</td>
                  <td>${carts[0].product.between.toLocaleString('tr-TR')}</td>
                </tr>`
                    total['quantity'] += productTotal['quantity']
                    total['weight'] += productTotal['weight']
                  })
                })
              })
              rows += 
                `<tr class="total">
                  <th>Toplam</th>`
              for (let i = HEIGHTS[type].min; i <= HEIGHTS[type].max; i += HEIGHTS[type].between) {
                rows += 
                  `<th>${total['height'][i].toLocaleString('tr-TR')}</th>`
              }
              rows += 
                  `<th></th>
                  <th>${total['quantity'].toLocaleString('tr-TR')}</th>
                  <th>${total['weight'].toLocaleString('tr-TR')}</th>
                  <th></th>
                </tr>`
            })
          })
        })      
      }
      $('tbody').html(rows)
      if (searchParamss['only'] != undefined) {
        let totalAll = {
          'quantity': 0,
          'weight': 0
        }
        $('tbody .total').each(function (i) {
          let total = 0,
            numbers = []
          $(this).find('th').each(function () {
            if ($.isNumeric($(this).text().replace('.', '').replace(',', '.'))) {
              total += Number($(this).text().replace('.', '').replace(',', '.'))
              numbers.push(Number($(this).text().replace('.', '').replace(',', '.')))
            }
          })
          if (total == 0) {
            $(this).hide()
            $('tbody .weight').eq(i).hide()
          } else {
            let max = numbers.sort((x, y) => y - x).slice(0, 2);
            totalAll.weight += max[0]
            totalAll.quantity += max[1]
          }
        })
        $('total-quantity').html(totalAll.quantity.toLocaleString('tr-TR'))
        $('total-weight').html(totalAll.weight.toLocaleString('tr-TR'))
      } else {
        $('total-quantity').html(resp.total.quantity.toLocaleString('tr-TR'))
        $('total-weight').html(resp.total.weight.toLocaleString('tr-TR'))
      }
      $('tbody tr').each(function () {
        if ($(this).attr('class') == undefined)
          $(this).remove()
      })
    },
    error: function (resp) {
      // alertCustom('danger', resp.responseJSON.message)
      $('tbody').html(`<tr><td>${resp.responseJSON.message}</td></tr>`)
    },
    complete: function () {
      $('.loading').removeClass('show z-1').addClass('z-n1')
      $('table').show()
    }
  })
</script>
@endsection