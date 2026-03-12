/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var templateSelect = document.getElementById('comment-field-template');
	if (templateSelect) {
		templateSelect.addEventListener('change', function () {
			var co = document.getElementById('comment-field-comment');

			if (this.value != 'mc') {
				var tmplEl = document.getElementById(this.value);
				if (tmplEl) {
					co.value = tmplEl.value;
				}
			} else {
				co.value = '';
			}
		});
	}

	var accessField = document.getElementById('comment-field-access');
	if (accessField) {
		accessField.addEventListener('click', function () {
			var es = document.getElementById('email_submitter');
			if (!es) return;

			if (this.checked) {
				if (es.checked === true) {
					es.checked = false;
					es.disabled = true;
				}
			} else {
				es.disabled = false;
			}
		});
	}

	var attach = document.getElementById('ajax-uploader');
	if (attach) {
		var uploaderList = document.getElementById('ajax-uploader-list');
		if (uploaderList) {
			uploaderList.addEventListener('click', function (e) {
				var link = e.target.closest('a.delete');
				if (!link) return;

				e.preventDefault();
				if (link.getAttribute('data-id')) {
					fetch(link.getAttribute('href'));
				}
				var row = link.parentNode.parentNode;
				if (row && row.parentNode) {
					row.parentNode.removeChild(row);
				}
			});
		}

		var running = 0;

		if (typeof qq !== 'undefined' && qq.FileUploader) {
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
				onSubmit: function (id, file) {
					running++;
				},
				onComplete: function (id, file, response) {
					running--;

					// HTML entities had to be encoded for the JSON or IE 8 went nuts.
					// So, now we have to decode it.
					response.html = response.html.replace(/&gt;/g, '>');
					response.html = response.html.replace(/&lt;/g, '<');

					var list = document.getElementById('ajax-uploader-list');
					if (list) {
						var temp = document.createElement('div');
						temp.innerHTML = response.html;
						while (temp.firstChild) {
							list.appendChild(temp.firstChild);
						}
					}

					if (running == 0) {
						var qqList = document.querySelector('ul.qq-upload-list');
						if (qqList) {
							qqList.innerHTML = '';
						}
					}
				}
			});
		}
	}
});
