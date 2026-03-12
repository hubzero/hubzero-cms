/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave', {bubbles: true}));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

function appendNoHtml(url) {
	if (url.indexOf('?') == -1) {
		return url + '?no_html=1';
	} else {
		return url + '&no_html=1';
	}
}

document.addEventListener('DOMContentLoaded', function(){
	var attach = document.getElementById('ajax-uploader');

	if (attach) {
		var uploader = new qq.FileUploader({
			element: attach,
			action: attach.getAttribute('data-action'),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
				'<div class="qq-upload-button"><span class="text-base-content">' + attach.getAttribute('data-instructions') + '</span></div>' +
				'<div class="qq-upload-drop-area"><span class="text-base-content">' + attach.getAttribute('data-instructions') + '</span></div>' +
				'<ul class="qq-upload-list"></ul>' +
			'</div>',
			onComplete: function(id, file, response) {
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
		var fileInput = attach.querySelector('input[type="file"]');
		if (fileInput) {
			fileInput.setAttribute('aria-label', 'Upload image');
		}
	}

	var imgDeleteBtn = document.getElementById('img-delete');
	if (imgDeleteBtn) {
		imgDeleteBtn.addEventListener('click', function (e) {
			e.preventDefault();

			var el = this;
			var currentfileEl = document.getElementById('currentfile');
			var currentfileVal = currentfileEl ? currentfileEl.value : '';

			var url = appendNoHtml(el.getAttribute('href'));
			fetch(url + '&currentfile=' + encodeURIComponent(currentfileVal))
				.then(function(res) { return res.json(); })
				.then(function(response) {
					if (response.success) {
						var imgDisplay = document.getElementById('img-display');
						if (imgDisplay) imgDisplay.setAttribute('src', el.getAttribute('data-noimg'));
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
