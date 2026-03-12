/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('adminForm');

	if (frm) {
		Hubzero.submitform(task, frm);
		return;
	}

	frm = document.getElementById('item-form');

	if (frm) {
		document.dispatchEvent(new Event('editorSave'));

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var batchSubmit = document.getElementById('btn-batch-submit');
	if (batchSubmit) {
		batchSubmit.addEventListener('click', function (e) {
			return Hubzero.submitbutton('category.batch');
		});
	}

	var batchClear = document.getElementById('btn-batch-clear');
	if (batchClear) {
		batchClear.addEventListener('click', function (e) {
			e.preventDefault();
			var catId = document.getElementById('batch-category-id');
			if (catId) catId.value = '';
			var access = document.getElementById('batch-access');
			if (access) access.value = '';
			var langId = document.getElementById('batch-language-id');
			if (langId) langId.value = '';
		});
	}
});
