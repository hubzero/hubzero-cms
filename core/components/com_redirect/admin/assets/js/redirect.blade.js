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

document.addEventListener('DOMContentLoaded', function() {
	var updateLinks = document.getElementById('update-links');

	if (updateLinks) {
		updateLinks.addEventListener('click', function(e) {
			this.form.task.value = 'activate';
			this.form.submit();
		});
	}
});
