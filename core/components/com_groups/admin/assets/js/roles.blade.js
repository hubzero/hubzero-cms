/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('component-form');

	if (!frm) {
		frm = document.getElementById('item-form');
	}

	if (frm) {
		document.dispatchEvent(new Event('editorSave'));

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);

			var redirect = frm.getAttribute('data-redirect');
			if (redirect) {
				window.top.setTimeout("window.parent.location='" + redirect + "'", 700);
			}
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
};

document.addEventListener('DOMContentLoaded', function() {
	var btnSave = document.getElementById('btn-save');
	if (btnSave) {
		btnSave.addEventListener('click', function(e) {
			Hubzero.submitbutton('delegate');
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function(e) {
			if (window.parent && window.parent.document) {
				window.parent.close();
			}
		});
	}
});
