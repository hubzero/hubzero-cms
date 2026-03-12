/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task, type) {
	var afrm = document.getElementById('adminForm');

	if (afrm) {
		Hubzero.submitform(task, afrm);
		return;
	}

	var frm = document.getElementById('item-form');

	if (frm) {
		document.dispatchEvent(new Event('editorSave'));
		if (task == 'cancel' || task == 'cancelemail' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function () {
	var batchSubmit = document.getElementById('btn-batch-submit');
	if (batchSubmit) {
		batchSubmit.addEventListener('click', function (e) {
			Hubzero.submitbutton('user.batch');
		});
	}

	var batchClear = document.getElementById('btn-batch-clear');
	if (batchClear) {
		batchClear.addEventListener('click', function (e) {
			e.preventDefault();
			document.getElementById('batch-group-id').value = '';
		});
	}

	var password = document.getElementById('newpass'),
		passrule = document.getElementById('passrules');

	if (password && passrule) {
		password.addEventListener('keyup', function () {
			fetch(password.getAttribute('data-href'), {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded',
					'Accept': 'application/json'
				},
				body: 'password1=' + encodeURIComponent(password.value)
					+ '&' + password.getAttribute('data-values')
			})
			.then(function (response) { return response.json(); })
			.then(function (json) {
				if (json.html.length > 0 && password.value !== '') {
					passrule.innerHTML = json.html;
				} else {
					passrule.querySelectorAll('li').forEach(function (li) {
						li.classList.remove('error', 'passed');
						li.classList.add('empty');
					});
				}
			});
		});
	}

	var classId = document.getElementById('class_id');
	if (classId) {
		classId.addEventListener('change', function (e) {
			fetch(this.getAttribute('data-href') + this.value, {
				headers: { 'Accept': 'application/json' }
			})
			.then(function (response) { return response.json(); })
			.then(function (data) {
				Object.keys(data).forEach(function (key) {
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

	// Organization Dropdown (vanilla JS autocomplete)
	if (document.querySelector('.rorApiAvailable')) {
		var orgInput = document.getElementById('profile_organization');
		if (orgInput) {
			var listId = 'org-autocomplete-list';
			var listEl = document.createElement('datalist');
			listEl.id = listId;
			orgInput.parentElement.appendChild(listEl);
			orgInput.setAttribute('list', listId);

			var debounceTimer = null;
			orgInput.addEventListener('input', function () {
				clearTimeout(debounceTimer);
				debounceTimer = setTimeout(function () {
					var rorURL = 'index.php?option=com_members&controller=members&task=getOrganizations&term='
						+ encodeURIComponent(orgInput.value);

					fetch(rorURL, {
						headers: { 'Accept': 'application/json' }
					})
					.then(function (response) { return response.json(); })
					.then(function (result) {
						listEl.innerHTML = '';
						if (Array.isArray(result)) {
							result.forEach(function (item) {
								var option = document.createElement('option');
								option.value = (typeof item === 'string') ? item : (item.label || item.value || '');
								listEl.appendChild(option);
							});
						}
					})
					.catch(function (error) {
						console.log(error);
					});
				}, 300);
			});
		}
	}
});
