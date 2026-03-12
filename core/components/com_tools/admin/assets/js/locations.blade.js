/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	document.dispatchEvent(new Event('editorSave'));

	var frm = document.getElementById('adminForm');

	if (frm) {
		if (task == 'cancel'
			|| document.formvalidator.isValid(frm)
		) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
};

function setTask(task) {
	var taskField = document.getElementById('task');
	if (taskField) {
		taskField.value = task;
	}
}

function saveAndUpdate() {
	Hubzero.submitbutton('save');
	window.parent.setTimeout(function() {
		var iframe = window.parent.document.getElementById('locationslist');
		if (iframe) {
			iframe.src = iframe.src + '&';
		}
		// Close the popup window
		if (window.parent && window.parent.close) {
			try { window.parent.close(); } catch (e) {}
		}
	}, 700);
}

document.addEventListener('DOMContentLoaded', function() {
	var editLinks = document.querySelectorAll('a.edit-asset');
	editLinks.forEach(function(link) {
		link.addEventListener('click', function(e) {
			e.preventDefault();
			window.open(
				link.getAttribute('href'),
				'editAsset',
				'width=570,height=550,scrollbars=yes,resizable=yes'
			);
		});
	});

	var from = document.getElementById('field-ipFROM');
	var to = document.getElementById('field-ipTO');

	if (from && to) {
		from.addEventListener('keyup', function() {
			var ipToRow = document.querySelectorAll('.ipTOrow');
			if (from.value.indexOf('/') !== -1) {
				ipToRow.forEach(function(el) {
					el.style.opacity = '0.3';
				});
				to.disabled = true;
			} else {
				ipToRow.forEach(function(el) {
					el.style.opacity = '1';
				});
				to.disabled = false;
			}
		});
	}

	var btnSave = document.getElementById('btn-save');
	if (btnSave) {
		btnSave.addEventListener('click', function() {
			saveAndUpdate();
		});
	}

	var btnClose = document.getElementById('btn-close');
	if (btnClose) {
		btnClose.addEventListener('click', function() {
			if (window.parent && window.parent.close) {
				try { window.parent.close(); } catch (e) {}
			}
		});
	}

	var continentcountry = [];
	var countrydata = document.getElementById('country-data');

	if (countrydata) {
		var k = 0;
		continentcountry[k++] = [
			'', '', countrydata.getAttribute('data-select')
		];

		var cdata = JSON.parse(countrydata.innerHTML);

		for (var i = 0; i < cdata.data.length; i++) {
			continentcountry[k++] = [
				cdata.data[i]['continent'],
				cdata.data[i]['code'],
				cdata.data[i]['name']
			];
		}
	}

	var continentField = document.getElementById('field-continent');
	if (continentField) {
		continentField.addEventListener('change', function() {
			var selectedVal =
				continentField.options[continentField.selectedIndex].value;
			changeDynaList(
				'field-countrySHORT',
				continentcountry,
				selectedVal,
				0,
				0
			);
		});
	}
});
