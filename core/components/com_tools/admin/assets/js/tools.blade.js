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
	var details = document.getElementById('details');
	if (details) {
		details.addEventListener('click', function(e) {
			e.preventDefault();
		});
	}

	var zones = document.getElementById('zones');
	if (zones) {
		zones.addEventListener('click', function(e) {
			e.preventDefault();
		});
	}

	var classId = document.getElementById('class_id');
	if (classId) {
		classId.addEventListener('change', function(e) {
			var parent = this.parentElement;
			var url = parent.getAttribute('data-href')
				+ '&class_id=' + this.value;

			fetch(url)
				.then(function(response) { return response.json(); })
				.then(function(data) {
					Object.keys(data).forEach(function(key) {
						var item = document.getElementById('field-' + key);
						if (item) {
							item.value = data[key];

							if (e.target.options[e.target.selectedIndex].text == 'custom') {
								item.readOnly = false;
							} else {
								item.readOnly = true;
							}
						}
					});
				});
		});
	}

	// Note: fancybox removed - edit-asset links will navigate normally
	document.querySelectorAll('a.edit-asset').forEach(function(el) {
		el.addEventListener('click', function(e) {
			e.preventDefault();
			window.open(
				this.getAttribute('href'),
				'editasset',
				'width=570,height=550,scrollbars=yes,resizable=yes'
			);
		});
	});
});
