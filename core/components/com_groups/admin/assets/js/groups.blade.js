/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var cfrm = document.getElementById('component-form');

	if (cfrm) {
		Hubzero.submitform(task, cfrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		if (!frm.task) {
			frm.task = {}
		}

		document.dispatchEvent(new Event('editorSave'));

		if (task == 'markscanned') {
			if (!confirm(frm.getAttribute('data-confirm'))) {
				return false;
			}
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var unblockLink = document.querySelector('#toolbar-unblock a');
	if (unblockLink) {
		unblockLink.addEventListener('click', function (e) {
			e.preventDefault();

			if (document.adminForm.boxchecked.value == 0) {
				alert('Please first make a selection from the list');
			} else {
				var serialized = '';
				document.querySelectorAll('input[type=checkbox]').forEach(function (cb) {
					if (cb.checked) {
						serialized += '&' + cb.name + '=' + cb.value;
					}
				});
				if (serialized) {
					var url = this.getAttribute('href') + serialized;
					window.open(url, 'unblock', 'width=400,height=400,scrollbars=yes,resizable=yes');
				}
			}
		});
	}

	var btnSave = document.getElementById('btn-save');
	if (btnSave) {
		btnSave.addEventListener('click', function (e) {
			Hubzero.submitbutton('save');
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function (e) {
			Hubzero.submitbutton('cancel');
		});
	}
});
