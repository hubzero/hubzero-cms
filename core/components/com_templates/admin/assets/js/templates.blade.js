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
	var toggleBtns = document.querySelectorAll('.jform-rightbtn');

	toggleBtns.forEach(function(btn) {
		btn.addEventListener('click', function(e) {
			e.preventDefault();

			var checkboxes = document.querySelectorAll('.chk-menulink');
			checkboxes.forEach(function(el) {
				el.checked = !el.checked;
			});
		});
	});
});
