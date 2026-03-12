/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	document.querySelectorAll('.delete-image').forEach(function (el) {
		el.addEventListener('click', function (e) {
			var target = document.getElementById('picture-' + e.target.id);
			if (target) {
				target.remove();
			}
		});
	});

	function readURL(input) {
		var files = Array.prototype.slice.call(input.files);
		files.forEach(function(file) {
			var reader = new FileReader();
			reader.onload = function(e) {
				var uploadImages = document.getElementById('uploadImages');
				if (uploadImages) {
					uploadImages.insertAdjacentHTML('beforeend', '<img src="' + e.target.result + '" width="100" height="100" alt="" />');
				}
			}
			reader.readAsDataURL(file);
		});
	}

	var imgInp = document.getElementById('imgInp');
	if (imgInp) {
		imgInp.addEventListener('change', function () {
			var uploadImages = document.getElementById('uploadImages');
			if (uploadImages) {
				uploadImages.innerHTML = '';
			}
			readURL(this);
		});
	}
});
