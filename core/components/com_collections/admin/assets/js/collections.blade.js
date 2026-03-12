/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
};

document.addEventListener('DOMContentLoaded', function () {
	var attach = document.getElementById('ajax-uploader');
	var list = document.getElementById('ajax-uploader-list');
	var linkr = document.getElementById('link-adder');

	if (list) {
		list.addEventListener('click', function (e) {
			var target = e.target;
			if (!target.matches('a.delete')) {
				return;
			}
			e.preventDefault();

			var dataId = target.getAttribute('data-id');
			if (dataId) {
				fetch(target.getAttribute('href'))
					.catch(function () {});
			}
			var row = target.closest('.item-asset');
			if (row) {
				row.remove();
			}
		});
	}

	if (linkr) {
		var linkerDiv = document.createElement('div');
		linkerDiv.className = 'linker';
		linkerDiv.innerHTML = '<div class="linker-button"><span>'
			+ linkr.getAttribute('data-txt-instructions') + '</span></div>';
		linkr.appendChild(linkerDiv);

		var linkerBtn = linkerDiv.querySelector('.linker-button');
		if (linkerBtn) {
			linkerBtn.addEventListener('click', function () {
				var i = document.querySelectorAll('.item-asset').length + 1000;
				var base = linkr.getAttribute('data-base');
				var txtDelete = linkr.getAttribute('data-txt-delete');

				var p = document.createElement('p');
				p.className = 'item-asset';
				p.innerHTML = '<span class="asset-handle"></span>'
					+ '<span class="asset-file">'
					+ '<input type="text" name="assets[' + i + '][filename]" size="35" value="http://" placeholder="http://" />'
					+ '</span>'
					+ '<span class="asset-description">'
					+ '<input type="hidden" name="assets[' + i + '][type]" value="link" />'
					+ '<input type="hidden" name="assets[' + i + '][id]" value="0" />'
					+ '<a class="delete" href="' + base + '" data-id="" title="' + txtDelete + '">' + txtDelete + '</a>'
					+ '</span>';

				if (list) {
					list.appendChild(p);
				}
			});
		}
	}

	if (attach && typeof qq !== 'undefined') {
		var uploader = new qq.FileUploader({
			element: attach,
			action: attach.getAttribute('data-action'),
			params: {
				dir: document.getElementById('field-dir')
					? document.getElementById('field-dir').value : '',
				i: document.querySelectorAll('.item-asset').length
			},
			multiple: true,
			debug: true,
			template: '<div class="qq-uploader">'
				+ '<div class="qq-upload-button"><span>' + attach.getAttribute('data-txt-instructions') + '</span></div>'
				+ '<div class="qq-upload-drop-area"><span>' + attach.getAttribute('data-txt-instructions') + '</span></div>'
				+ '<ul class="qq-upload-list"></ul>'
				+ '</div>',
			onComplete: function (id, file, response) {
				var fieldDir = document.getElementById('field-dir');
				var fieldId = document.getElementById('field-id');

				if (fieldDir && response.id != fieldDir.value) {
					if (fieldId) {
						fieldId.value = response.id;
					}
					fieldDir.value = response.id;
					uploader.setParams({ dir: fieldDir.value });
				}

				response.html = response.html.replace(/&gt;/g, '>');
				response.html = response.html.replace(/&lt;/g, '<');

				if (list) {
					var temp = document.createElement('div');
					temp.innerHTML = response.html;
					while (temp.firstChild) {
						list.appendChild(temp.firstChild);
					}
				}
			}
		});
	}
});
