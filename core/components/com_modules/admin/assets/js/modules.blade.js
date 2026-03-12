/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		document.dispatchEvent(new Event('editorSave'));

		if (task == 'cancel' || task == 'module.cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
			if (self != top) {
				window.top.setTimeout(function() {
					window.parent.close();
				}, 1000);
			}
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

function validate() {
	var assignmentEl = document.getElementById('jform_assignment');
	var list = document.getElementById('menu-assignment');

	if (!assignmentEl) {
		return;
	}

	var value = assignmentEl.value;
	var buttons = document.querySelectorAll('.jform-assignments-button');
	var inputs = list ? list.querySelectorAll('input') : [];

	if (value == '-' || value == '0') {
		buttons.forEach(function(el) {
			el.disabled = true;
		});
		inputs.forEach(function(el) {
			el.disabled = true;
			if (value == '-') {
				el.checked = false;
			} else {
				el.checked = true;
			}
		});
	} else {
		buttons.forEach(function(el) {
			el.disabled = false;
		});
		inputs.forEach(function(el) {
			el.disabled = false;
		});
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var itemForm = document.getElementById('item-form');

	if (itemForm) {
		validate();
		var selects = itemForm.querySelectorAll('select');
		selects.forEach(function(sel) {
			sel.addEventListener('change', function(e) {
				validate();
			});
		});
	}

	var batchSubmit = document.getElementById('btn-batch-submit');

	if (batchSubmit) {
		batchSubmit.addEventListener('click', function(e) {
			Hubzero.submitbutton('batch');
		});
	}

	var batchClear = document.getElementById('btn-batch-clear');

	if (batchClear) {
		batchClear.addEventListener('click', function(e) {
			e.preventDefault();
			var posId = document.getElementById('batch-position-id');
			var access = document.getElementById('batch-access');
			var langId = document.getElementById('batch-language-id');
			if (posId) posId.value = '';
			if (access) access.value = '';
			if (langId) langId.value = '';
		});
	}

	var moduleOrderData = document.getElementById('moduleorder');

	if (moduleOrderData) {
		var modorders = JSON.parse(moduleOrderData.innerHTML);

		var html = '\n	<select name="' + modorders.name + '" id="' + modorders.id + '"' + modorders.attr + '>';
		var i = 0;
		var key = modorders.originalPos;
		var orig_key = modorders.originalPos;
		var orig_val = modorders.originalOrder;

		for (var x in modorders.orders) {
			if (modorders.orders[x][0] == key) {
				var selected = '';
				if ((orig_key == key && orig_val == modorders.orders[x][1])
				 || (i == 0 && orig_key != key)) {
					selected = 'selected="selected"';
				}
				html += '\n		<option value="' + modorders.orders[x][1] + '" ' + selected + '>' + modorders.orders[x][2] + '</option>';
			}
			i++;
		}
		html += '\n	</select>';

		moduleOrderData.insertAdjacentHTML('afterend', html);
	}
});
