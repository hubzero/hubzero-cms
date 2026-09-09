/**
 * Blog media manager — display panel logic.
 *
 * Handles parent-iframe resizing, file-detail panel population,
 * insert/copy/delete actions, and upload picker UX.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

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
  window.showFileDetail = function (data) {
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
  };

  document.addEventListener('DOMContentLoaded', function () {
    // Insert into editor — one parent hop (display is in filer iframe, edit page is parent)
    document.getElementById('btn-insert').addEventListener('click', function () {
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
        setTimeout(function () { btn.querySelector('span').textContent = 'Insert'; }, 1500);
      } catch (err) {
        document.getElementById('btn-copy').click();
      }
    });

    // Copy to clipboard with selected/active look
    document.getElementById('btn-copy').addEventListener('click', function () {
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
      setTimeout(function () {
        btn.classList.remove('btn-success');
        btn.querySelector('span').textContent = 'Copy';
      }, 3000);
    });

    // Delete with confirmation — navigate to trigger server-side delete
    document.getElementById('btn-delete').addEventListener('click', function () {
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
    picker.addEventListener('click', function () {
      pickerIcon.classList.add('hidden');
      pickerSpinner.classList.remove('hidden');
      pickerText.textContent = 'Opening file browser\u2026';
      picker.classList.add('border-primary/40', 'bg-base-200/50');
      // Revert if dialog cancelled (browser refocuses window)
      function onFocusBack() {
        window.removeEventListener('focus', onFocusBack);
        setTimeout(function () {
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
    uploadInput.addEventListener('change', function () {
      if (this.files && this.files[0]) {
        pickerIcon.classList.add('hidden');
        pickerSpinner.classList.remove('hidden');
        pickerText.textContent = 'Uploading ' + this.files[0].name + '\u2026';
        picker.classList.add('border-primary/40', 'bg-base-200/50');
        picker.style.pointerEvents = 'none';
        document.getElementById('adminForm').submit();
      }
    });
  });
})();
