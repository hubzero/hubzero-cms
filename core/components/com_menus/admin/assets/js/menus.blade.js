/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task, type) {
	type = type || '';

	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		document.dispatchEvent(new Event('editorSave'));

		if (task == 'items.setType' || task == 'items.setMenuType') {
			if (task == 'items.setType') {
				var typeInput = frm.querySelector('input[name="fields[type]"]');
				if (typeInput) {
					typeInput.value = type;
				}
				var fieldType = document.getElementById('fieldtype');
				if (fieldType) {
					fieldType.value = 'type';
				}
			} else {
				var menuTypeInput = frm.querySelector('input[name="fields[menutype]"]');
				if (menuTypeInput) {
					menuTypeInput.value = type;
				}
			}
			Hubzero.submitform('items.setType', frm);
		} else if (task == 'cancel' || task == 'items.cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			var invalids = frm.querySelectorAll('.modal-value.invalid');

			if (invalids.length) {
				invalids.forEach(function(field) {
					var idReversed = field.id.split("").reverse().join("");
					var separatorLocation = idReversed.indexOf('_');
					var name = idReversed.substr(separatorLocation).split("").reverse().join("") + 'name';
					var nameEl = document.getElementById(name);
					if (nameEl) {
						nameEl.classList.add('invalid');
					}
				});
			} else {
				alert(frm.getAttribute('data-invalid-msg'));
			}
		}
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var showMods = document.getElementById('showmods');

	if (showMods) {
		showMods.addEventListener('click', function(e) {
			var rows = document.querySelectorAll('.adminlist tr.nope');
			rows.forEach(function(row) {
				if (row.style.display === 'none') {
					row.style.display = '';
				} else {
					row.style.display = 'none';
				}
			});
		});
	}

	var batchSubmit = document.getElementById('btn-batch-submit');

	if (batchSubmit) {
		batchSubmit.addEventListener('click', function(e) {
			Hubzero.submitbutton('item.batch');
		});
	}

	var batchClear = document.getElementById('btn-batch-clear');

	if (batchClear) {
		batchClear.addEventListener('click', function(e) {
			e.preventDefault();
			var batchMenu = document.getElementById('batch-menu-id');
			var batchAccess = document.getElementById('batch-access');
			var batchLang = document.getElementById('batch-language-id');
			if (batchMenu) batchMenu.value = '';
			if (batchAccess) batchAccess.value = '';
			if (batchLang) batchLang.value = '';
		});
	}
});
