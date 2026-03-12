/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function (task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
};

var Fields = {
	initialise: function () {
		var eventField = document.getElementById('field-event');
		if (eventField) {
			eventField.addEventListener('change', function () {
				var ev = this.value.replace('::', '--');

				document.querySelectorAll('fieldset.eventparams').forEach(function (el) {
					el.style.display = 'none';
				});

				var target = document.getElementById('params-' + ev);
				if (target) {
					target.style.display = 'block';
				}
			});
		}

		var recurrenceField = document.getElementById('field-recurrence');
		if (recurrenceField) {
			recurrenceField.addEventListener('change', function () {
				var min = '*',
					hour = '*',
					day = '*',
					month = '*',
					dow = '*',
					recurrence = this.value;

				switch (recurrence) {
					case '0 0 1 1 *':
						min = '0';
						hour = '0';
						day = '1';
						month = '1';
						break;
					case '0 0 1 * *':
						min = '0';
						hour = '0';
						day = '1';
						break;
					case '0 0 * * 0':
						min = '0';
						hour = '0';
						dow = '0';
						break;
					case '0 0 * * *':
						min = '0';
						hour = '0';
						break;
					case '0 * * * *':
						min = '0';
						break;
				}

				var customEl = document.getElementById('custom');
				if (customEl) {
					if (recurrence == 'custom') {
						customEl.classList.remove('hide');
					} else {
						customEl.classList.add('hide');
					}
				}

				Fields._setVal('field-minute-c', min);
				Fields._setVal('field-minute-s', min);
				Fields._setVal('field-hour-c', hour);
				Fields._setVal('field-hour-s', hour);
				Fields._setVal('field-day-c', day);
				Fields._setVal('field-day-s', day);
				Fields._setVal('field-month-c', month);
				Fields._setVal('field-month-s', month);
				Fields._setVal('field-dayofweek-c', dow);
				Fields._setVal('field-dayofweek-s', dow);
			});
		}

		// Sync paired cron fields
		var pairs = ['minute', 'hour', 'day', 'month', 'dayofweek'];
		pairs.forEach(function (name) {
			var sEl = document.getElementById('field-' + name + '-s');
			var cEl = document.getElementById('field-' + name + '-c');
			var recEl = document.getElementById('field-recurrence');

			if (sEl) {
				sEl.addEventListener('change', function () {
					if (cEl) {
						cEl.value = this.value;
					}
					if (recEl) {
						recEl.value = 'custom';
					}
				});
			}
			if (cEl) {
				cEl.addEventListener('change', function () {
					if (sEl) {
						sEl.value = this.value;
					}
					if (recEl) {
						recEl.value = 'custom';
					}
				});
			}
		});
	},

	_setVal: function (id, val) {
		var el = document.getElementById(id);
		if (el) {
			el.value = val;
		}
	}
};

document.addEventListener('DOMContentLoaded', function () {
	Fields.initialise();
});
