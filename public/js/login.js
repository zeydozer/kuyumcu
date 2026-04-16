function alertCustom(type, message) {
  $('.toast').attr('class', 'toast').addClass('text-bg-' + type)
  $('.toast-body').html(message)
  bootstrap.Toast.getOrCreateInstance($('.toast')).show()
}

function resolveErrorMessage(resp) {
  return resp?.responseJSON?.message || 'Giri\u015F s\u0131ras\u0131nda bir hata olu\u015Ftu.'
}

function setFeedback(message) {
  $('#login-feedback').html(message).addClass('is-visible')
}

function clearFeedback() {
  $('#login-feedback').removeClass('is-visible').empty()
}

let spinner = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>'

$('[data-password-toggle]').on('click', function () {
  let input = $(this).siblings('input'),
    showPassword = input.attr('type') === 'password'

  input.attr('type', showPassword ? 'text' : 'password')
  $(this)
    .text(showPassword ? 'Gizle' : 'G\u00F6ster')
    .attr('aria-label', showPassword ? 'Parolay\u0131 gizle' : 'Parolay\u0131 g\u00F6ster')
})

$('[data-forgot-password]').on('click', function () {
  alertCustom('warning', '\u015Eifre s\u0131f\u0131rlama i\u00E7in y\u00F6neticinizle ileti\u015Fime ge\u00E7in.')
})

$('#login').submit(function (e) {
  e.preventDefault()
  clearFeedback()

  let datas = new FormData(this),
    button = $(this).find('button[type="submit"]'),
    buttonHtml = button.html()

  button
    .attr('disabled', true)
    .attr('aria-busy', true)
    .html(`${spinner}<span class="ms-2">Giri\u015F yap\u0131l\u0131yor</span>`)

  $.ajax({
    type: 'POST',
    url: '/api/login',
    data: datas,
    contentType: false,
    processData: false,
    cache: false,
    success: function (resp) {
      resp._token = TOKEN
      $.post('/token', resp, function () {
        location.reload()
      }).fail(function (respToken) {
        let message = resolveErrorMessage(respToken)
        setFeedback(message)
        alertCustom('danger', message)
      })
    },
    error: function (resp) {
      let message = resolveErrorMessage(resp)
      setFeedback(message)
      alertCustom('danger', message)
    },
    complete: function () {
      button
        .removeAttr('disabled')
        .removeAttr('aria-busy')
        .html(buttonHtml)
    }
  })
})
