/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function() {
	var datetimeFields = document.querySelectorAll('.datetime');
	datetimeFields.forEach(function(el) {
		flatpickr(el, {
			enableTime: true,
			time_24hr: true,
			minuteIncrement: 15,
			dateFormat: 'Y-m-d H:i:S',
			defaultHour: 8,
			defaultMinute: 0
		});
	});

	var btnSave = document.getElementById('btn-save');
	if (btnSave) {
		btnSave.addEventListener('click', function() {
			Hubzero.submitbutton('saveZone');
			window.parent.setTimeout(function() {
				var iframe =
					window.parent.document.getElementById('zoneslist');
				if (iframe) {
					iframe.src = iframe.src + '&';
				}
				// Close the popup window
				if (window.parent && window.parent.close) {
					try { window.parent.close(); } catch (e) {}
				}
			}, 700);
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function() {
			if (window.parent && window.parent.close) {
				try { window.parent.close(); } catch (e) {}
			}
		});
	}

	var wsEnable = document.getElementById(
		'field-zone-params-websocket-enable'
	);
	if (wsEnable) {
		wsEnable.addEventListener('click', function() {
			var wsFields = document.querySelectorAll('.websocket');
			wsFields.forEach(function(field) {
				field.disabled = !field.disabled;
				field.classList.toggle('opaque');
			});
		});
	}

	var vncEnable = document.getElementById(
		'field-zone-params-vnc-enable'
	);
	if (vncEnable) {
		vncEnable.addEventListener('click', function() {
			var vncFields = document.querySelectorAll('.vnc');
			vncFields.forEach(function(field) {
				field.disabled = !field.disabled;
				field.classList.toggle('opaque');
			});
		});
	}

	var upload = document.getElementById('ajax-uploader');
	if (upload && typeof qq !== 'undefined') {
		var uploader = new qq.FileUploader({
			element: upload,
			action: upload.getAttribute('data-action'),
			multiple: true,
			debug: true,
			template:
				'<div class="qq-uploader">' +
					'<div class="qq-upload-button"><span>' +
						upload.getAttribute('data-instructions') +
					'</span></div>' +
					'<div class="qq-upload-drop-area"><span>' +
						upload.getAttribute('data-instructions') +
					'</span></div>' +
					'<ul class="qq-upload-list"></ul>' +
				'</div>',
			onComplete: function(id, file, response) {
				if (response.success) {
					var imgDisplay =
						document.getElementById('img-display');
					var imgName = document.getElementById('img-name');
					var imgSize = document.getElementById('img-size');
					var imgWidth = document.getElementById('img-width');
					var imgHeight = document.getElementById('img-height');
					var imgDelete = document.getElementById('img-delete');

					if (imgDisplay) {
						imgDisplay.setAttribute(
							'src',
							'..' + response.directory +
								'/' + response.file
						);
					}
					if (imgName) {
						imgName.textContent = response.file;
					}
					if (imgSize) {
						imgSize.textContent = response.size;
					}
					if (imgWidth) {
						imgWidth.textContent = response.width;
					}
					if (imgHeight) {
						imgHeight.textContent = response.height;
					}
					if (imgDelete) {
						imgDelete.style.display = '';
					}
				}
			}
		});
	}

	var imgDelete = document.getElementById('img-delete');
	if (imgDelete) {
		imgDelete.addEventListener('click', function(e) {
			e.preventDefault();

			var href = imgDelete.getAttribute('href');
			// Append no-html parameter
			var separator = href.indexOf('?') === -1 ? '?' : '&';
			var url = href + separator + 'no_html=1&format=json';

			fetch(url)
				.then(function(response) {
					return response.json();
				})
				.then(function(response) {
					if (response.success) {
						var imgDisplay =
							document.getElementById('img-display');
						var imgName = document.getElementById('img-name');
						var imgSize = document.getElementById('img-size');
						var imgWidth =
							document.getElementById('img-width');
						var imgHeight =
							document.getElementById('img-height');

						if (imgDisplay) {
							imgDisplay.setAttribute(
								'src', '../media/images/blank.png'
							);
						}
						if (imgName) {
							imgName.textContent = '[ none ]';
						}
						if (imgSize) {
							imgSize.textContent = '0';
						}
						if (imgWidth) {
							imgWidth.textContent = '0';
						}
						if (imgHeight) {
							imgHeight.textContent = '0';
						}
					}
					imgDelete.style.display = 'none';
				});
		});
	}
});
