/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');
	if (!frm) {
		frm = document.getElementById('component-form');
	}

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

function nohtml(url) {
	if (url.indexOf('?') == -1) {
		return url + '?no_html=1';
	} else {
		return url + '&no_html=1';
	}
}

document.addEventListener('DOMContentLoaded', function() {
	// Asset edit links — open in new window instead of fancybox
	if (document.getElementById('adminForm')) {
		document.querySelectorAll('a.edit-asset').forEach(function(link) {
			link.addEventListener('click', function(e) {
				e.preventDefault();
				window.open(this.getAttribute('href'), '_blank', 'width=570,height=550');
			});
		});
	}

	if (document.getElementById('component-form')) {
		var btnSave = document.getElementById('btn-save');
		if (btnSave) {
			btnSave.addEventListener('click', function(e) {
				Hubzero.submitbutton('save');

				window.top.setTimeout(function() {
					var assetsFrame = window.parent.document.getElementById('assets');
					if (assetsFrame) {
						assetsFrame.src = assetsFrame.src;
					}
					if (window.parent && window.parent.close) {
						window.close();
					}
				}, 700);
			});
		}

		var btnCancel = document.getElementById('btn-cancel');
		if (btnCancel) {
			btnCancel.addEventListener('click', function(e) {
				Hubzero.submitbutton('cancel');
				window.close();
			});
		}

		var btnGenerate = document.getElementById('btn-generate');
		if (btnGenerate) {
			btnGenerate.addEventListener('click', function(e) {
				Hubzero.submitbutton('generate');
				window.top.setTimeout(function() {
					window.parent.location = btnGenerate.getAttribute('data-redirect');
				}, 700);
			});
		}

		var btnAttach = document.getElementById('btn-attach');
		if (btnAttach) {
			btnAttach.addEventListener('click', function(e) {
				document.getElementById('task').value = 'link';
			});
		}
	}

	// File uploader
	var attach = document.getElementById('ajax-uploader');
	if (attach) {
		var uploader = new qq.FileUploader({
			element: attach,
			action: attach.getAttribute('data-action'),
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">' +
						'<div class="qq-upload-button"><span>' + attach.getAttribute('data-instructions') + '</span></div>' +
						'<div class="qq-upload-drop-area"><span>' + attach.getAttribute('data-instructions') + '</span></div>' +
						'<ul class="qq-upload-list"></ul>' +
					'</div>',
			onComplete: function(id, file, response) {
				if (response.success) {
					var imgDisplay = document.getElementById('img-display');
					if (imgDisplay) {
						imgDisplay.setAttribute('src', '..' + response.directory + '/' + response.file);
						var imgName = document.getElementById('img-name');
						if (imgName) imgName.textContent = response.file;
						var imgSize = document.getElementById('img-size');
						if (imgSize) imgSize.textContent = response.size;
						var imgWidth = document.getElementById('img-width');
						if (imgWidth) imgWidth.textContent = response.width;
						var imgHeight = document.getElementById('img-height');
						if (imgHeight) imgHeight.textContent = response.height;

						var imgDelete = document.getElementById('img-delete');
						if (imgDelete) imgDelete.style.display = '';
					}
				}
			}
		});

		// WCAG: label dynamically-created file inputs
		var fileInput = attach.querySelector('input[type="file"]');
		if (fileInput) {
			fileInput.setAttribute('aria-label', 'Upload file');
		}
	}

	var imgDelete = document.getElementById('img-delete');
	if (imgDelete) {
		imgDelete.addEventListener('click', function(e) {
			e.preventDefault();
			var el = this;
			fetch(nohtml(el.getAttribute('href')), { headers: { 'Accept': 'application/json' } })
				.then(function(r) { return r.json(); })
				.then(function(response) {
					if (response.success) {
						var imgDisplay = document.getElementById('img-display');
						if (imgDisplay) imgDisplay.setAttribute('src', el.getAttribute('data-defaultimg'));
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

	// Offering/section dynamic list
	var offeringId = document.getElementById('offering_id');
	var sectionId = document.getElementById('section_id');
	if (offeringId && sectionId) {
		var offeringsections = [];
		var dataEl = document.getElementById('offering-data');

		if (dataEl) {
			var raw = dataEl.content ? dataEl.content.textContent : dataEl.innerHTML;
			offeringsections = JSON.parse(raw);
		}

		offeringId.addEventListener('change', function(e) {
			changeDynaList(
				'section_id',
				offeringsections['data'],
				offeringId.options[offeringId.selectedIndex].value,
				0,
				0
			);
		});
	}

	// Flatpickr for datetime fields
	if (typeof flatpickr !== 'undefined') {
		document.querySelectorAll('.datetime-field').forEach(function(field) {
			flatpickr(field, {
				enableTime: true,
				time_24hr: true,
				dateFormat: 'Y-m-d H:i:S',
				allowInput: true
			});
		});
	}

	// Badge toggle
	var badgePublished = document.getElementById('badge-published');
	if (badgePublished) {
		var badgeFields = document.querySelectorAll('.badge-field-toggle');
		if (!badgePublished.checked) {
			badgeFields.forEach(function(el) { el.style.display = 'none'; });
		}

		badgePublished.addEventListener('click', function(e) {
			badgeFields.forEach(function(el) {
				el.style.display = el.style.display === 'none' ? '' : 'none';
			});
		});
	}

	// WCAG: label dynamically created file inputs (MutationObserver)
	var observer = new MutationObserver(function() {
		var inputs = document.querySelectorAll('.qq-upload-button input[type="file"]:not([aria-label])');
		for (var i = 0; i < inputs.length; i++) {
			inputs[i].setAttribute('aria-label', 'Upload file');
		}
	});
	observer.observe(document.body, { childList: true, subtree: true });
});
