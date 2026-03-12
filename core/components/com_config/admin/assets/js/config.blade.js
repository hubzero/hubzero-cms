/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	document.dispatchEvent(new Event('editorSave'));

	var frma = document.getElementById('application-form');

	if (frma) {
		if (task == 'application.cancel' || document.formvalidator.isValid(frma)) {
			Hubzero.submitform(task, frma);
		} else {
			alert(frma.getAttribute('data-invalid-msg'));
		}
	}

	var frmc = document.getElementById('component-form');

	if (frmc) {
		if (task == 'cancel' || document.formvalidator.isValid(frmc)) {
			Hubzero.submitform(task, frmc);
		} else {
			alert(frmc.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var frmc = document.getElementById('component-form');

	if (frmc) {
		var btnApply = document.getElementById('btn-apply');
		if (btnApply) {
			btnApply.addEventListener('click', function (e) {
				Hubzero.submitform('component.apply', frmc);
			});
		}

		var btnSave = document.getElementById('btn-save');
		if (btnSave) {
			btnSave.addEventListener('click', function (e) {
				Hubzero.submitform('component.save', frmc);
			});
		}

		var btnCancel = document.getElementById('btn-cancel');
		if (btnCancel) {
			btnCancel.addEventListener('click', function (e) {
				if (this.getAttribute('data-refresh')) {
					window.parent.location.href = window.parent.location.href;
				}
				window.close();
			});
		}
	}
});
