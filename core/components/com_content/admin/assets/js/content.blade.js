/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	var frm = document.getElementById('adminForm');

	if (frm) {
		return Hubzero.submitform(task, frm);
	}

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
	var batchSubmit = document.getElementById('btn-batch-submit');
	if (batchSubmit) {
		batchSubmit.addEventListener('click', function () {
			Hubzero.submitbutton('article.batch');
		});
	}

	var batchClear = document.getElementById('btn-batch-clear');
	if (batchClear) {
		batchClear.addEventListener('click', function (e) {
			e.preventDefault();
			var catId = document.getElementById('batch-category-id');
			var access = document.getElementById('batch-access');
			var langId = document.getElementById('batch-language-id');

			if (catId) {
				catId.value = '';
			}
			if (access) {
				access.value = '';
			}
			if (langId) {
				langId.value = '';
			}
		});
	}
});
