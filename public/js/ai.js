const $messages = $('#ai-messages')
const $input = $('#ai-input')
const $send = $('#ai-send')
const $reset = $('#ai-reset')
const $mic = $('#ai-mic')
const $attach = $('#ai-attach')
const $file = $('#ai-file')
const $attachment = $('#ai-attachment')
const $attachmentName = $('#ai-attachment-name')
const $attachmentRemove = $('#ai-attachment-remove')

const DEFAULT_MESSAGE =
  'Merhaba! Sipari\u015f durumu, \u00fcr\u00fcn arama veya raporlama konular\u0131nda yard\u0131mc\u0131 olabilirim.'
const MAX_FILE_SIZE = 20 * 1024 * 1024

let attachedFile = null

function scrollBottom() {
  $messages.scrollTop($messages[0].scrollHeight)
}

function appendMsg(text, role) {
  const cls = role === 'user' ? 'ai-msg--user' : 'ai-msg--bot'
  const $el = $(`<div class="ai-msg ${cls}"></div>`).text(text)
  $messages.append($el)
  scrollBottom()
  return $el
}

function appendLoading() {
  const $el = $('<div class="ai-msg ai-msg--loading">Yan\u0131t haz\u0131rlan\u0131yor</div>')
  $messages.append($el)
  scrollBottom()
  return $el
}

function updateSendState() {
  $send.prop('disabled', !$input.val().trim() && !attachedFile)
}

function clearAttachment() {
  attachedFile = null
  $file.val('')
  $attachment.attr('hidden', true)
  $attachmentName.text('')
  updateSendState()
}

function setAttachment(file) {
  attachedFile = file
  $attachmentName.text(file.name)
  $attachment.removeAttr('hidden')
  updateSendState()
}

function buildUserMessage(text) {
  if (!attachedFile) {
    return text
  }

  if (!text) {
    return `Ekli dosya: ${attachedFile.name}`
  }

  return `${text}\n\nEkli dosya: ${attachedFile.name}`
}

function getErrorMessage(xhr) {
  if (xhr.responseJSON && xhr.responseJSON.message) {
    return xhr.responseJSON.message
  }

  return 'Bir hata olu\u015ftu. L\u00fctfen tekrar deneyin.'
}

function sendMessage() {
  const text = $input.val().trim()

  if (!text && !attachedFile) {
    return
  }

  appendMsg(buildUserMessage(text), 'user')
  $input.val('')
  $send.prop('disabled', true)

  const $loading = appendLoading()
  const formData = new FormData()

  formData.append('_token', TOKEN)

  if (text) {
    formData.append('message', text)
  }

  if (attachedFile) {
    formData.append('attachment', attachedFile)
  }

  $.ajax({
    url: '/ai/chat',
    method: 'POST',
    data: formData,
    processData: false,
    contentType: false,
    success: function (res) {
      $loading.remove()
      appendMsg(res.reply, 'bot')
      clearAttachment()
    },
    error: function (xhr) {
      $loading.remove()
      appendMsg(getErrorMessage(xhr), 'bot')
      updateSendState()
    },
    complete: function () {
      $input.focus()
    }
  })
}

$reset.click(function () {
  $messages.empty()
  appendMsg(DEFAULT_MESSAGE, 'bot')
  clearAttachment()
  $.post('/ai/reset', { _token: TOKEN })
})

$attach.click(function () {
  $file.trigger('click')
})

$file.change(function () {
  const file = this.files && this.files[0]

  if (!file) {
    return
  }

  if (file.size > MAX_FILE_SIZE) {
    clearAttachment()
    alertCustom('warning', 'Dosya boyutu en fazla 20 MB olabilir.')
    return
  }

  setAttachment(file)
  $input.focus()
})

$attachmentRemove.click(function () {
  clearAttachment()
  $input.focus()
})

$send.click(sendMessage)

$input.keydown(function (e) {
  if (e.key === 'Enter' && !e.shiftKey) {
    e.preventDefault()
    sendMessage()
  }
})

$input.on('input', updateSendState)
updateSendState()

if ('webkitSpeechRecognition' in window || 'SpeechRecognition' in window) {
  const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition
  const recognition = new SpeechRecognition()
  recognition.lang = 'tr-TR'
  recognition.interimResults = false

  $mic.click(function () {
    if ($mic.hasClass('active')) {
      recognition.stop()
    } else {
      recognition.start()
      $mic.addClass('active')
    }
  })

  recognition.onresult = function (e) {
    const transcript = e.results[0][0].transcript
    $input.val(`${$input.val()}${transcript}`)
    updateSendState()
  }

  recognition.onend = function () {
    $mic.removeClass('active')
  }

  recognition.onerror = function () {
    $mic.removeClass('active')
  }
} else {
  $mic.hide()
}

$input.focus()
scrollBottom()
