{{--
  Blog media manager — upload form + file listing iframe + detail panel.

  Variables from controller (displayTask):
    $archive  — Archive model instance
    $option   — Component option string
    $controller — Controller name

  @package    hubzero-cms
  @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
  @license    http://opensource.org/licenses/MIT MIT
--}}
@php
  use Hubzero\Facades\Html;
  use Hubzero\Facades\Lang;
  use Hubzero\Facades\Request;

  $base = rtrim(Request::base(true), '/');

  $uploadUrl = $base
      . '/index.php?option=' . $option
      . '&tmpl=component&controller=' . $controller
      . '&task=upload';

  $listUrl = $base
      . '/index.php?option=' . $option
      . '&tmpl=component&controller=' . $controller
      . '&task=list'
      . '&scope=' . urlencode($archive->get('scope'))
      . '&id=' . $archive->get('scope_id');
@endphp

<div id="attachments" class="space-y-3 pb-2">
  <form action="{{ $uploadUrl }}"
        id="adminForm"
        method="post"
        enctype="multipart/form-data"
        class="space-y-3">

    {{-- File listing iframe --}}
    <div class="border border-base-300 rounded-lg overflow-hidden">
      <iframe src="{{ $listUrl }}"
              name="imgManager"
              id="imgManager"
              class="w-full border-0"
              height="260"
              title="{{ Lang::txt('COM_BLOG_FIELD_FILES') }}"></iframe>
    </div>

    {{-- Upload dropzone — auto-submits on file selection --}}
    <label id="upload-picker"
           class="flex flex-col items-center justify-center gap-1 px-3 py-3
                  border-2 border-dashed border-base-300 rounded-lg
                  cursor-pointer hover:border-primary/40 hover:bg-base-200/50
                  transition-colors">
      <span class="flex items-center gap-2 text-sm text-base-content/60">
        <svg id="picker-icon" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
             stroke-width="1.5" stroke="currentColor"
             class="w-5 h-5" aria-hidden="true">
          <path stroke-linecap="round" stroke-linejoin="round"
                d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5m-13.5-9L12 3m0 0 4.5 4.5M12 3v13.5" />
        </svg>
        <span id="picker-spinner" class="loading loading-spinner loading-sm hidden" aria-hidden="true"></span>
        <span id="picker-text">Choose a file to upload</span>
      </span>
      <span class="text-xs text-base-content/40">File uploads automatically after selection</span>
      <input type="file" name="upload" id="upload" class="hidden" />
    </label>

    <input type="hidden" name="option" value="{{ e($option) }}" />
    <input type="hidden" name="controller" value="{{ e($controller) }}" />
    <input type="hidden" name="task" value="upload" />
    <input type="hidden" name="scope" value="{{ e($archive->get('scope')) }}" />
    <input type="hidden" name="id" value="{{ e($archive->get('scope_id')) }}" />
    <input type="hidden" name="tmpl" value="component" />
    {!! Html::input('token') !!}
  </form>

  {{-- File detail panel (shown when a file is selected in the list) --}}
  <div id="file-detail" class="hidden rounded-lg mt-4 overflow-hidden
                                border border-primary/30
                                shadow-[0_0_0_1px_rgba(var(--color-primary)/0.08),0_2px_8px_rgba(0,0,0,0.08)]">
    <div class="bg-base-200 px-3 py-3 border-b border-primary/15">
      <p class="text-sm font-semibold break-all leading-tight" id="detail-filename"></p>
      <div class="flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-base-content/50 mt-1.5">
        <span id="detail-size"></span>
        <span id="detail-ext"></span>
        <span id="detail-date"></span>
        <span id="detail-image-badge"
              class="hidden text-success font-medium">Image</span>
      </div>
    </div>
    <div class="grid grid-cols-3 gap-1 px-2 py-2 bg-base-100">
      <button type="button" id="btn-insert"
              class="btn btn-xs btn-ghost border-base-300">
        <span>Insert</span>
      </button>
      <button type="button" id="btn-copy"
              class="btn btn-xs btn-ghost border-base-300">
        <span>Copy</span>
      </button>
      <button type="button" id="btn-delete"
              class="btn btn-xs btn-ghost border-base-300 text-error">
        <span>Delete</span>
      </button>
    </div>
    {{-- Macro preview --}}
    <div class="px-3 py-2 bg-base-200/50 border-t border-base-300/50">
      <p class="text-[0.65rem] text-base-content/40 mb-1">Inserts as:</p>
      <code id="detail-ref"
            class="block text-[0.65rem] bg-base-300/40 rounded px-1.5 py-1
                   break-all leading-snug text-base-content/60"></code>
    </div>
  </div>

  @if($__view->getError())
    <div class="alert alert-error" role="alert">
      {{ $__view->getError() }}
    </div>
  @endif

  {{-- Usage hint --}}
  <div class="text-[0.7rem] text-base-content/35 mt-3 px-1 leading-snug space-y-1.5">
    <p>Select a file, then <strong>Insert</strong> to add at
      cursor or <strong>Copy</strong> to paste manually.</p>
    <div class="mt-1">
      <p class="mb-0.5">Syntax for images:</p>
      <code class="block bg-base-200 px-1 py-0.5 rounded">&#91;[Image(file.jpg)]]</code>
      <p class="mb-0.5 mt-1.5">or for other files:</p>
      <code class="block bg-base-200 px-1 py-0.5 rounded">&#91;[File(doc.pdf)]]</code>
    </div>
  </div>
</div>

<script>
var currentRef = '';
var currentConfirm = '';

// Resize the parent filer iframe to fit content (no scrollbar)
function resizeFilerIframe() {
  try {
    var filer = window.parent.document.getElementById('filer');
    if (filer) {
      filer.style.height = document.documentElement.scrollHeight + 'px';
    }
  } catch (e) {}
}

// Called by child iframe (list.blade.php) when a file is selected
function showFileDetail(data) {
  document.getElementById('detail-filename').textContent = data.filename;
  document.getElementById('detail-size').textContent = data.size;
  document.getElementById('detail-ext').textContent = data.ext;
  document.getElementById('detail-date').textContent = data.date;
  currentRef = data.ref;
  currentConfirm = data.confirm;
  document.getElementById('btn-delete').dataset.deleteUrl = data.deleteUrl;
  document.getElementById('detail-ref').textContent = data.ref;
  // Show/hide image badge
  var badge = document.getElementById('detail-image-badge');
  if (data.isImage) {
    badge.classList.remove('hidden');
  } else {
    badge.classList.add('hidden');
  }
  // Reset copy button state
  var copyBtn = document.getElementById('btn-copy');
  copyBtn.classList.remove('btn-success');
  copyBtn.querySelector('span').textContent = 'Copy';
  // Reset insert button state
  var insertBtn = document.getElementById('btn-insert');
  insertBtn.querySelector('span').textContent = 'Insert';
  // Show panel and resize iframe to fit
  document.getElementById('file-detail').classList.remove('hidden');
  setTimeout(resizeFilerIframe, 50);
}

// Insert into editor — one parent hop (display is in filer iframe, edit page is parent)
document.getElementById('btn-insert').addEventListener('click', function() {
  var btn = this;
  try {
    var topWin = window.parent;
    var field = topWin.document.getElementById('entrycontent');
    if (!field) return;
    if (typeof topWin.insertAtCursor === 'function') {
      topWin.insertAtCursor(field, currentRef);
    } else {
      var s = field.selectionStart, e = field.selectionEnd;
      field.value = field.value.substring(0, s) + currentRef
        + field.value.substring(e);
      field.selectionStart = field.selectionEnd = s + currentRef.length;
    }
    field.focus();
    btn.querySelector('span').textContent = 'Inserted!';
    setTimeout(function() { btn.querySelector('span').textContent = 'Insert'; }, 1500);
  } catch (err) {
    document.getElementById('btn-copy').click();
  }
});

// Copy to clipboard with selected/active look
document.getElementById('btn-copy').addEventListener('click', function() {
  var btn = this;
  if (navigator.clipboard) {
    navigator.clipboard.writeText(currentRef);
  } else {
    var ta = document.createElement('textarea');
    ta.value = currentRef;
    ta.style.position = 'fixed';
    ta.style.left = '-9999px';
    document.body.appendChild(ta);
    ta.select();
    document.execCommand('copy');
    document.body.removeChild(ta);
  }
  btn.classList.add('btn-success');
  btn.querySelector('span').textContent = 'Copied!';
  setTimeout(function() {
    btn.classList.remove('btn-success');
    btn.querySelector('span').textContent = 'Copy';
  }, 3000);
});

// Delete with confirmation — navigate to trigger server-side delete
document.getElementById('btn-delete').addEventListener('click', function() {
  if (confirm(currentConfirm)) {
    window.location.href = this.dataset.deleteUrl;
  }
});

// Upload: auto-submit on file selection
var uploadInput = document.getElementById('upload');
var picker = document.getElementById('upload-picker');
var pickerText = document.getElementById('picker-text');
var pickerIcon = document.getElementById('picker-icon');
var pickerSpinner = document.getElementById('picker-spinner');

// Show spinner on click while file dialog is open
picker.addEventListener('click', function() {
  pickerIcon.classList.add('hidden');
  pickerSpinner.classList.remove('hidden');
  pickerText.textContent = 'Opening file browser\u2026';
  picker.classList.add('border-primary/40', 'bg-base-200/50');
  // Revert if dialog cancelled (browser refocuses window)
  function onFocusBack() {
    window.removeEventListener('focus', onFocusBack);
    setTimeout(function() {
      if (!uploadInput.files || !uploadInput.files.length) {
        pickerSpinner.classList.add('hidden');
        pickerIcon.classList.remove('hidden');
        pickerText.textContent = 'Choose a file to upload';
        picker.classList.remove('border-primary/40', 'bg-base-200/50');
      }
    }, 300);
  }
  window.addEventListener('focus', onFocusBack);
});

// File selected — show uploading state and submit immediately
uploadInput.addEventListener('change', function() {
  if (this.files && this.files[0]) {
    pickerIcon.classList.add('hidden');
    pickerSpinner.classList.remove('hidden');
    pickerText.textContent = 'Uploading ' + this.files[0].name + '\u2026';
    picker.classList.add('border-primary/40', 'bg-base-200/50');
    picker.style.pointerEvents = 'none';
    document.getElementById('adminForm').submit();
  }
});
</script>
