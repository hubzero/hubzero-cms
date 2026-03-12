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

function addtag(tag)
{
	var input = document.getElementById('tags-men');
	if (input.value == '') {
		input.value = tag;
	} else {
		input.value += ', ' + tag;
	}
}

document.addEventListener('DOMContentLoaded', function() {
	// CSP-safe handler for file options button
	document.addEventListener('click', function(e) {
		var btn = e.target.closest('[data-action="do-fileoptions"]');
		if (btn) {
			e.preventDefault();
			if (typeof doFileoptions === 'function') {
				doFileoptions();
			}
		}
	});

	document.querySelectorAll('.addtag').forEach(function(el) {
		el.addEventListener('click', function(e) {
			e.preventDefault();
			addtag(this.getAttribute('data-tag'));
		});
	});

	document.querySelectorAll('#reset_ranking, #reset_rating, #reset_hits').forEach(function(el) {
		el.addEventListener('click', function(e) {
			e.preventDefault();
			Hubzero.submitbutton(this.getAttribute('data-task'));
		});
	});

	document.querySelectorAll('.btn-ratings').forEach(function(el) {
		el.addEventListener('click', function(e) {
			e.preventDefault();
			window.open(
				this.getAttribute('href'),
				'ratings',
				'status=no,toolbar=no,scrollbars=yes,titlebar=no,menubar=no,resizable=yes,width=400,height=480,directories=no,location=no'
			);
		});
	});

	var fields = document.getElementById('fields');

	if (fields) {
		// Delegated change handler on select elements within #fields
		fields.addEventListener('change', function(e) {
			if (e.target.tagName !== 'SELECT') return;
			var select = e.target;
			var i = select.name.replace(/^fields\[(\d+)\]\[type\]/g, "$1");
			var url = fields.getAttribute('data-href')
				+ '&type=' + select.value + '&name=' + i;

			fetch(url)
				.then(function(response) { return response.text(); })
				.then(function(html) {
					var target = document.getElementById('fields-' + i + '-options');
					if (target) {
						target.innerHTML = html;
					}
				});
		});

		// Delegated click handler for adding custom options
		fields.addEventListener('click', function(e) {
			var btn = e.target.closest('.add-custom-option');
			if (!btn) return;
			e.preventDefault();

			var id = btn.getAttribute('data-rel');
			if (!id) return;

			var table = document.getElementById(id);
			if (!table) return;

			var tbody = table.tBodies[0];
			var counter = tbody.rows.length;
			var newNode = tbody.rows[0].cloneNode(true);
			var newField = newNode.childNodes;

			for (var i = 0; i < newField.length; i++) {
				var inputs = newField[i].childNodes;
				for (var k = 0; k < inputs.length; k++) {
					var theName = inputs[k].name;
					if (theName) {
						var tokens = theName.split('[');
						var n = tokens[2];
						inputs[k].name = 'fields[' + id + '][' + n + '[' + counter + '][label]';
					}
					if (inputs[k].value) {
						inputs[k].value = '';
					}
				}
			}

			tbody.appendChild(newNode);
		});

		var addFieldBtn = document.getElementById('add-custom-field');
		if (addFieldBtn) {
			addFieldBtn.addEventListener('click', function(e) {
				e.preventDefault();

				var id = 'fields';
				var tbody = document.getElementById(id).tBodies[0];
				var counter = tbody.rows.length;
				var newNode = tbody.rows[0].cloneNode(true);
				var newField = newNode.childNodes;

				for (var i = 0; i < newField.length; i++) {
					var inputs = newField[i].childNodes;
					for (var k = 0; k < inputs.length; k++) {
						var theName = inputs[k].name;
						if (theName) {
							var tokens = theName.split('[');
							var n = tokens[2];
							inputs[k].name = id + '[' + counter + '][' + n;
							inputs[k].id = id + '-' + counter + '-' + n.replace(']', '');
						}
						var z = id + '[' + counter + '][required]';
						if (inputs[k].value && inputs[k].name != z) {
							inputs[k].value = '';
							inputs[k].selectedIndex = 0;
							inputs[k].selected = false;
						}
						if (inputs[k].checked) {
							inputs[k].checked = false;
						}
					}
					if (newField[i].id) {
						newField[i].id = 'fields-' + counter + '-options';
					}
				}

				tbody.appendChild(newNode);
			});
		}
	}
});
