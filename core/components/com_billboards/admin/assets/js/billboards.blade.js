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
	var stylingTable = document.getElementById('styling_table');

	if (stylingTable) {
		stylingTable.style.display = 'none';

		var stylingBtn = document.getElementById('styling');

		if (stylingBtn) {
			stylingBtn.addEventListener('click', function(e) {
				e.preventDefault();

				if (stylingTable.style.display === 'none') {
					stylingTable.style.display = '';
				} else {
					stylingTable.style.display = 'none';
				}
			});
		}
	}
});
