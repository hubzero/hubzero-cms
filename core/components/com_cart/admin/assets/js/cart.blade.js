/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		Hubzero.submitform(task, frm);
	}
};

document.addEventListener('DOMContentLoaded', function () {
	var fromEl = document.getElementById('filter-report-from');
	var toEl = document.getElementById('filter-report-to');

	if (fromEl && typeof flatpickr !== 'undefined') {
		var fpFrom = flatpickr(fromEl, {
			dateFormat: 'm/d/Y',
			onChange: function (selectedDates) {
				if (selectedDates.length && fpTo) {
					fpTo.set('minDate', selectedDates[0]);
				}
			}
		});

		var fpTo = flatpickr(toEl, {
			dateFormat: 'm/d/Y',
			onChange: function (selectedDates) {
				if (selectedDates.length && fpFrom) {
					fpFrom.set('maxDate', selectedDates[0]);
				}
			}
		});
	}

	var pIdClear = document.getElementById('filter_pId-clear');
	if (pIdClear) {
		pIdClear.addEventListener('click', function () {
			var pId = document.getElementById('filter_pId');
			if (pId) {
				pId.value = '';
			}
			var sId = document.getElementById('filter_sId');
			if (sId) {
				sId.value = '';
			}
			this.form.submit();
		});
	}

	var uidClear = document.getElementById('filter_uidNumber-clear');
	if (uidClear) {
		uidClear.addEventListener('click', function () {
			var uid = document.getElementById('filter_uidNumber');
			if (uid) {
				uid.value = '';
			}
			this.form.submit();
		});
	}
});
