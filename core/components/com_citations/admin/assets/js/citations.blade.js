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

function citeaddRow(id) {
	var table = document.getElementById(id);
	var rows = table.querySelectorAll('tbody tr');
	var tr = rows[rows.length - 1];
	var clone = tr.cloneNode(true);
	var cindex = rows.length;
	var inputs = clone.querySelectorAll('input,select');

	inputs.forEach(function (el) {
		el.value = '';
		el.setAttribute('name', el.getAttribute('name').replace(/\[\d+\]/, '[' + cindex + ']'));
	});
	tr.parentNode.insertBefore(clone, tr.nextSibling);
}

document.addEventListener('DOMContentLoaded', function () {
	var addRow = document.getElementById('add_row');
	if (addRow) {
		addRow.addEventListener('click', function (e) {
			e.preventDefault();

			citeaddRow('assocs');
			return false;
		});
	}

	var formatSelector = document.getElementById('format-selector'),
		formatBox = document.getElementById('format-string');

	if (formatSelector && formatBox) {
		//when we change format box
		formatSelector.addEventListener('change', function (event) {
			var value = this.value,
				selected = this.options[this.selectedIndex],
				format = selected.getAttribute('data-format');
			formatBox.value = format;
		});

		//when we customize the format
		formatBox.addEventListener('keyup', function (event) {
			var customOption = formatSelector.querySelector('option[value=custom]');
			if (customOption) {
				customOption.setAttribute('data-format', formatBox.value);
			}
		});

		document.querySelectorAll('#preformatted tr').forEach(function (row) {
			row.addEventListener('click', function (e) {
				formatBox.value = formatBox.value + this.id;
				formatBox.focus();
			});
		});
	}
});
