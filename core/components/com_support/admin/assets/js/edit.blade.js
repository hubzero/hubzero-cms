/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (!frm) {
		frm = document.getElementById('component-form');
	}

	if (!frm) {
		frm = document.getElementById('adminForm');
	}

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
};

/**
 * Toggles the check state of a group of boxes
 *
 * Checkboxes must have an id attribute in the form cb0, cb1...
 * @param The number of box to 'check'
 * @param An alternative field name
 */
function checkAllOptions() {
	var f = document.adminForm;
	var c = f.toggleOpt.checked;

	var chks = document.querySelectorAll('.chk');
	for (var i = 0; i < chks.length; i++) {
		chks[i].checked = c;
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var toggleOpt = document.getElementById('toggleOpt');
	if (toggleOpt) {
		toggleOpt.addEventListener('change', function () {
			checkAllOptions();
		});
	}

	var newacl = document.getElementById('newacl');
	if (newacl) {
		newacl.addEventListener('click', function (e) {
			e.preventDefault();

			var frm = document.getElementById('adminForm');
			Hubzero.submitform('save', frm);
		});
	}

	var col = document.getElementById('field-color');
	if (col && typeof col.colpick === 'function') {
		col.colpick({
			layout: 'hex',
			colorScheme: 'dark',
			submit: 1,
			onSubmit: function (hsb, hex, rgb, el) {
				col.value = hex;
			}
		});
	}

	var actionBtns = document.querySelectorAll('#btn-apply, #btn-save');
	for (var i = 0; i < actionBtns.length; i++) {
		actionBtns[i].addEventListener('click', function () {
			Hubzero.submitbutton(this.getAttribute('data-task'));
		});
	}

	var btnCancel = document.getElementById('btn-cancel');
	if (btnCancel) {
		btnCancel.addEventListener('click', function () {
			if (this.getAttribute('data-refresh') == 'true') {
				window.parent.location.href = window.parent.location.href;
			}
			window.parent.postMessage('admin-popup-close', '*');
		});
	}
});
