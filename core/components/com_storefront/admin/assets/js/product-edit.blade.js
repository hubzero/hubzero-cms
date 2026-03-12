/**
 * Storefront Product — Edit page behavior
 *
 * Handles form validation, access group checkbox cascading,
 * and image upload/delete via AJAX.
 *
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */
(function () {
  'use strict';

  // ------------------------------------------------------------------
  // submitbutton override — validate required fields before submit
  // ------------------------------------------------------------------
  window.submitbutton = function (pressbutton) {
    if (pressbutton === 'cancel') {
      Hubzero.submitform(pressbutton);
      return;
    }

    var title = document.getElementById('field-title');
    var tagline = document.getElementById('field-pTagline');
    var description = document.getElementById('field-description');

    if (title && title.value === '') {
      alert('Title cannot be empty');
    } else if (tagline && tagline.value === '') {
      alert('Tagline cannot be empty');
    } else if (description && description.value === '') {
      alert('Description cannot be empty');
    } else {
      Hubzero.submitform(pressbutton);
    }
  };

  Hubzero.submitbutton = window.submitbutton;

  // ------------------------------------------------------------------
  // Access group checkbox cascading (check descendents on change)
  // ------------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    var groups = document.querySelectorAll('.usergroups');
    if (!groups.length) return;

    groups.forEach(function (groupEl) {
      var boxes = groupEl.querySelectorAll('input[type="checkbox"]');

      boxes.forEach(function (box) {
        box.addEventListener('change', function () {
          checkDescendants(boxes, this);
        });
      });

      function checkDescendants(allBoxes, el) {
        var rel = el.id;
        if (el.checked && rel) {
          allBoxes.forEach(function (child) {
            if (child.getAttribute('rel') === rel) {
              child.checked = true;
              checkDescendants(allBoxes, child);
            }
          });
        }
      }
    });
  });

  // ------------------------------------------------------------------
  // Image uploader + delete (requires qq.FileUploader)
  // ------------------------------------------------------------------
  document.addEventListener('DOMContentLoaded', function () {
    var uploaderEl = document.getElementById('ajax-uploader');
    if (!uploaderEl) return;

    var configEl = document.getElementById('product-image-config');
    var noImgSrc = configEl ? configEl.getAttribute('data-no-image') : '';

    if (typeof qq !== 'undefined') {
      var uploader = new qq.FileUploader({
        element: uploaderEl,
        action: uploaderEl.getAttribute('data-action'),
        multiple: true,
        debug: true,
        template:
          '<div class="qq-uploader">' +
          '<div class="qq-upload-button">' +
          '<span class="text-base-content">' + (uploaderEl.getAttribute('data-upload-text') || 'Click or drop file') + '</span>' +
          '</div>' +
          '<div class="qq-upload-drop-area">' +
          '<span class="text-base-content">' + (uploaderEl.getAttribute('data-upload-text') || 'Click or drop file') + '</span>' +
          '</div>' +
          '<ul class="qq-upload-list"></ul>' +
          '</div>',
        onComplete: function (id, file, response) {
          if (response.success) {
            var imgDisplay = document.getElementById('img-display');
            if (imgDisplay) imgDisplay.setAttribute('src', '..' + response.directory + '/' + response.file);
            var imgName = document.getElementById('img-name');
            if (imgName) imgName.textContent = response.file;
            var imgSize = document.getElementById('img-size');
            if (imgSize) imgSize.textContent = response.size;
            var imgWidth = document.getElementById('img-width');
            if (imgWidth) imgWidth.textContent = response.width;
            var imgHeight = document.getElementById('img-height');
            if (imgHeight) imgHeight.textContent = response.height;
            var currentfile = document.getElementById('currentfile');
            if (currentfile) currentfile.value = response.imgId;
            var imgDelete = document.getElementById('img-delete');
            if (imgDelete) imgDelete.style.display = '';
          }
        }
      });

      // WCAG: label the dynamically-created file input
      var fileInput = uploaderEl.querySelector('input[type="file"]');
      if (fileInput) {
        fileInput.setAttribute('aria-label', 'Upload product image');
      }
    }

    var imgDeleteBtn = document.getElementById('img-delete');
    if (imgDeleteBtn) {
      imgDeleteBtn.addEventListener('click', function (e) {
        e.preventDefault();
        var el = this;
        var currentfileVal = document.getElementById('currentfile').value;
        var href = el.getAttribute('href');
        if (href.indexOf('?') === -1) {
          href += '?no_html=1';
        } else {
          href += '&no_html=1';
        }
        fetch(href + '&currentfile=' + encodeURIComponent(currentfileVal))
          .then(function (res) { return res.json(); })
          .then(function (response) {
            if (response.success) {
              var imgDisplay = document.getElementById('img-display');
              if (imgDisplay) imgDisplay.setAttribute('src', noImgSrc);
              var imgName = document.getElementById('img-name');
              if (imgName) imgName.textContent = '[ none ]';
              var imgSize = document.getElementById('img-size');
              if (imgSize) imgSize.textContent = '0';
              var imgWidth = document.getElementById('img-width');
              if (imgWidth) imgWidth.textContent = '0';
              var imgHeight = document.getElementById('img-height');
              if (imgHeight) imgHeight.textContent = '0';
            }
            el.style.display = 'none';
          });
      });
    }
  });
})();
