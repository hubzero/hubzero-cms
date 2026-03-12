/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

Hubzero.submitbutton = function(task) {
	var frm = document.getElementById('item-form');

	if (frm) {
		if (task == 'cancel') {
			Hubzero.submitform(task, frm);
			return;
		}

		if (task == 'delete') {
			frm.admin_action.value = 'delete';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'suspend') {
			frm.admin_action.value = 'suspend';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'reinstate') {
			frm.admin_action.value = 'reinstate';
			Hubzero.submitform('save', frm);
			return;
		}

		if (task == 'cancel' || document.formvalidator.isValid(frm)) {
			Hubzero.submitform(task, frm);
		} else {
			alert(frm.getAttribute('data-invalid-msg'));
		}
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var password = document.getElementById('newpass');
	var passrule = document.getElementById('passrules');

	if (password && passrule) {
		password.addEventListener('keyup', function() {
			var url = password.getAttribute('data-href');
			var body = 'password1=' + encodeURIComponent(password.value)
				+ '&' + password.getAttribute('data-values');

			fetch(url, {
				method: 'POST',
				headers: {
					'Content-Type': 'application/x-www-form-urlencoded'
				},
				body: body
			})
			.then(function(response) { return response.json(); })
			.then(function(json) {
				if (json.html.length > 0 && password.value !== '') {
					passrule.innerHTML = json.html;
				} else {
					passrule.querySelectorAll('li').forEach(function(li) {
						li.classList.remove('error', 'passed');
						li.classList.add('empty');
					});
				}
			})
			.catch(function(err) {
				console.log(err);
			});
		});
	}

	var doDelete = document.getElementById('do-delete');
	if (doDelete) {
		doDelete.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton('delete');
		});
	}

	var doUnarchive = document.getElementById('do-unarchive');
	if (doUnarchive) {
		doUnarchive.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton('unarchive');
		});
	}

	var doArchive = document.getElementById('do-archive');
	if (doArchive) {
		doArchive.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton('archive');
		});
	}

	var doReinstate = document.getElementById('do-reinstate');
	if (doReinstate) {
		doReinstate.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton('reinstate');
		});
	}

	var doSuspend = document.getElementById('do-suspend');
	if (doSuspend) {
		doSuspend.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton('suspend');
		});
	}
});

// Grant agency autocomplete (vanilla JS)
(function () {
	var input = document.getElementById('param-grant_agency');
	if (!input) return;

	var timer = null;
	var dropdown = document.createElement('ul');
	dropdown.className = 'dropdown-content menu bg-base-100 shadow-lg rounded-box z-50 max-h-60 overflow-y-auto';
	dropdown.style.cssText = 'position:absolute;display:none;width:' + input.offsetWidth + 'px;';
	input.parentNode.style.position = 'relative';
	input.parentNode.appendChild(dropdown);

	input.addEventListener('input', function () {
		clearTimeout(timer);
		var val = input.value.trim();
		if (val.length < 2) { dropdown.style.display = 'none'; return; }

		timer = setTimeout(function () {
			var url = 'index.php?option=com_projects&controller=projects&task=getGrantAgency&term=' + encodeURIComponent(val);
			fetch(url)
				.then(function(response) { return response.json(); })
				.then(function(result) {
					dropdown.innerHTML = '';
					if (!result || !result.length) { dropdown.style.display = 'none'; return; }
					result.forEach(function (item) {
						var label = typeof item === 'string' ? item : (item.label || item.value || item);
						var value = typeof item === 'string' ? item : (item.value || item.label || item);
						var li = document.createElement('li');
						var a = document.createElement('a');
						a.textContent = label;
						a.addEventListener('mousedown', function (e) {
							e.preventDefault();
							input.value = value;
							dropdown.style.display = 'none';
						});
						li.appendChild(a);
						dropdown.appendChild(li);
					});
					dropdown.style.width = input.offsetWidth + 'px';
					dropdown.style.display = '';
				})
				.catch(function(err) {
					console.log(err);
					dropdown.style.display = 'none';
				});
		}, 300);
	});

	input.addEventListener('blur', function () {
		setTimeout(function () { dropdown.style.display = 'none'; }, 200);
	});
})();
