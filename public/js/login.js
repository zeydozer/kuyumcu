function alertCustom(type, message) {
  $('.toast').attr('class', 'toast').addClass('text-bg-'+ type)
  $('.toast-body').html(message)
  bootstrap.Toast.getOrCreateInstance($('.toast')).show()
}
let spinner = `<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>`
$('#login').submit(function (e) {
  e.preventDefault()
  let datas = new FormData(this),
    button = $(this).find('button'),
    buttonHtml = button.html()
  button.attr('disabled', true).html(spinner)
  $.ajax({
    type: 'POST',
    url: '/api/login',
    data: datas,
    contentType: false,
    processData: false,
    cache: false,
    success: function (resp) {
      resp._token = TOKEN
      $.post('/token', resp, function (respToken) {
        location.reload()
      }).fail(function (respToken) {
        alertCustom('danger', respToken.responseJSON.message)
      })
    },
    error: function (resp) {
      alertCustom('danger', resp.responseJSON.message)
    },
    complete: function () {
      button.removeAttr('disabled').html(buttonHtml)
    }
  })
})