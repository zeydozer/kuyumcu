@extends('main')

@section('title', 'Yapay Zeka')

@section('css')
<link rel="stylesheet" href="/css/ai.css?v=4">
@endsection

@section('content')
<div class="ai-shell">
  <div class="ai-page">
    <div class="ai-topbar">
      <span class="ai-title">Yapay Zeka Asistan</span>
      <button class="ai-reset" id="ai-reset">S&#305;f&#305;rla</button>
    </div>

    <div class="ai-messages" id="ai-messages">
      <div class="ai-msg ai-msg--bot">
        Merhaba! Sipari&#351; durumu, &#252;r&#252;n arama veya raporlama konular&#305;nda yard&#305;mc&#305; olabilirim.
      </div>
    </div>

    <div class="ai-attachment" id="ai-attachment" hidden>
      <span class="ai-attachment-name" id="ai-attachment-name"></span>
      <button class="ai-attachment-remove" id="ai-attachment-remove" type="button" aria-label="Eki kald&#305;r">
        <i class="bi-x-lg"></i>
      </button>
    </div>

    <div class="ai-inputbar">
      <button class="ai-inputbtn" id="ai-attach" type="button" aria-label="Dosya ekle">
        <i class="bi-paperclip"></i>
      </button>
      <button class="ai-inputbtn" id="ai-mic" type="button" aria-label="Sesli giri&#351;">
        <i class="bi-mic"></i>
      </button>
      <input
        class="ai-input"
        id="ai-input"
        type="text"
        placeholder="Sipari&#351;, &#252;r&#252;n veya rapor ile ilgili bir &#351;ey sorun..."
        autocomplete="off"
      >
      <button class="ai-send" id="ai-send" type="button" aria-label="G&#246;nder">
        <i class="bi-arrow-up"></i>
      </button>
    </div>

    <input
      id="ai-file"
      type="file"
      hidden
    >
  </div>
</div>
@endsection

@section('js')
<script src="/js/ai.js?v=4"></script>
@endsection
