function alertCustom(type, message) {
  $('.alert .toast').attr('class', 'toast').addClass('text-bg-' + type)
  $('.alert .toast-body').html(message)
  bootstrap.Toast.getOrCreateInstance($('.alert .toast')).show()
}
let spinner = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
$('.navbar .nav-link').each(function () {
  let currentPath = $(this).attr('href').split('?')[0]
  if (currentPath == '/' + location.pathname.split('/')[1])
    $(this).addClass('active')
})
$('#mode').click(function () {
  let mode = $(this).find('i').attr('class').split('-').pop()
  $.post('/mode', { type: mode, _token: TOKEN }, function () {
    $('html').attr('data-bs-theme', mode == 'moon' ? 'dark' : 'light')
    let modeSvg = 
      mode == 'moon' ?
      `<i class="bi-sun"></i>` :
      `<i class="bi-moon"></i>`;
    $('#mode').html(modeSvg)
  })
  return false
})
$('.modal [required]').each(function () {
  let tagName = $(this).prop('tagName').toLowerCase()
  if (tagName == 'input')
    $(this).attr('placeholder', $(this).attr('placeholder') + ' *')
  else if (tagName == 'select')
    $(this).find('option').eq(0).text($(this).find('option').eq(0).text() + ' *')
  if ($(this).closest('div').attr('class').indexOf('input-group') != -1)
    $(this).closest('div').find('.input-group-text').text($(this).closest('div').find('.input-group-text').text() + ' *')
})
function pagination(resp) {
  if (resp.last_page > 1) {
    $('total').html(resp.total.toLocaleString('tr-TR'))
    for (let i = 1; i <= resp.last_page; i++)
      $('#select-page').append(`<option>${i}</option>`)
    $('[name=page]').val(resp.current_page)
    $('#pagination').show()
    if (resp.current_page == 1)
      $('#prev-page').hide()
    else if (resp.current_page == resp.last_page)
      $('#next-page').hide()
  }
}
$('#select-page').change(function () {
  $('#current-page').val($(this).val())
  let form = $(SEARCH_FORM)
  if (form.prop('tagName') == 'FORM')
    $(SEARCH_FORM).trigger('submit')
  else
    $(SEARCH_FORM).find('form').trigger('submit')
})
$('#prev-page').click(function (e) {
  e.preventDefault()
  let currentPage = parseInt($('#select-page').val())
  $('#current-page').val(currentPage - 1)
  let form = $(SEARCH_FORM)
  if (form.prop('tagName') == 'FORM')
    $(SEARCH_FORM).trigger('submit')
  else
    $(SEARCH_FORM).find('form').trigger('submit')
})
$('#next-page').click(function (e) {
  e.preventDefault()
  let currentPage = parseInt($('#select-page').val())
  $('#current-page').val(currentPage + 1)
  let form = $(SEARCH_FORM)
  if (form.prop('tagName') == 'FORM')
    $(SEARCH_FORM).trigger('submit')
  else
    $(SEARCH_FORM).find('form').trigger('submit')
})
let searchParams
function setSearchParams() {
  searchParams = Object.fromEntries(new URLSearchParams(location.search))
  for (let name in searchParams)
    $(SEARCH_FORM + ' [name="' + name + '"]').val(searchParams[name])
}
$('th[sort]').click(function () {
  $('#current-sort').val($(this).attr('sort'))
  $('#select-page').trigger('change')
})
function setSort() {
  if (searchParams.sort != undefined && searchParams.sort != '') {
    let sort = searchParams.sort.split(' '),
      change = 
        sort[1] == 'asc' ?
        ['desc', 'up']:
        ['asc', 'down']
    $('th[sort]').each(function () {
      if ($(this).attr('sort').split(' ')[0] == sort[0]) {
        $(this).attr('sort', searchParams.sort.replace(sort[1], change[0]))
        if ($(this).find('i').length == 0)
          $(this).append(`\n<i class="bi-arrow-${change[1]}"></i>`)
        else
          $(this).find('i').attr('class', `bi-arrow-${change[1]}`)
      } else if ($(this).find('i').length > 0)
        $(this).find('i').remove()
    })
  }
}
const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(tooltipTriggerEl))