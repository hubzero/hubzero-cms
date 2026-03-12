/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var Fields = {
	addRow: function(id) {
		var tbody = document.getElementById(id).tBodies[0],
			counter = tbody.rows.length,
			newNode = tbody.rows[0].cloneNode(true),
			newField = newNode.childNodes;

		for (var i = 0; i < newField.length; i++) {
			var inputs = newField[i].childNodes;
			for (var k = 0; k < inputs.length; k++) {
				var theName = inputs[k].name;
				if (theName) {
					var tokens = theName.split('[');
					var n = tokens[2];
					inputs[k].name = id + '[' + counter + '][' + n;
					inputs[k].id = id + '-' + counter + '-'
						+ n.replace(']', '');
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

		Fields.initSelect();

		return false;
	},

	addOption: function(id) {
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
					inputs[k].name =
						'fields[' + id + '][' + n + '[' + counter + '][label]';
				}
				if (inputs[k].value) {
					inputs[k].value = '';
				}
			}
		}

		tbody.appendChild(newNode);

		return false;
	},

	initOptions: function() {
		var btns = document.querySelectorAll('.add-custom-option');
		btns.forEach(function(el) {
			// Remove old listeners by cloning
			var newEl = el.cloneNode(true);
			el.parentNode.replaceChild(newEl, el);
			newEl.addEventListener('click', function(e) {
				e.preventDefault();
				Fields.addOption(this.getAttribute('rel'));
			});
		});
	},

	timer: 0,

	clear: function() {
		Fields.timer = 0;
	},

	initSelect: function() {
		var selects = document.querySelectorAll('#fields select');
		selects.forEach(function(el) {
			// Remove old listeners by cloning
			var newEl = el.cloneNode(true);
			el.parentNode.replaceChild(newEl, el);
			newEl.addEventListener('change', function() {
				var selectEl = this;
				var i = selectEl.name.replace(
					/^fields\[(\d+)\]\[type\]/g, "$1"
				);
				var href = document.getElementById('fields')
					.getAttribute('data-href');
				var url = href + '&type=' + selectEl.value + '&name=' + i;

				fetch(url)
					.then(function(response) {
						return response.text();
					})
					.then(function(html) {
						var target = document.getElementById(
							'fields-' + i + '-options'
						);
						if (target) {
							target.innerHTML = html;
						}
						Fields.initOptions();
					});
			});
		});
	},

	initialise: function() {
		var addBtn = document.getElementById('add-custom-field');
		if (addBtn) {
			addBtn.addEventListener('click', function(e) {
				e.preventDefault();
				Fields.addRow('fields');
			});
		}

		Fields.initSelect();
		Fields.initOptions();

		// Drag-and-drop sortable for fields tbody
		Fields.initSortable();
	},

	initSortable: function() {
		var tbody = document.querySelector('#fields tbody');
		if (!tbody) {
			return;
		}

		var dragRow = null;

		tbody.addEventListener('mousedown', function(e) {
			var handle = e.target.closest('.handle');
			if (!handle) {
				return;
			}
			dragRow = handle.closest('tr');
			if (!dragRow) {
				return;
			}
			dragRow.style.opacity = '0.5';

			var onMouseMove = function(e2) {
				e2.preventDefault();
				var afterElement = getDragAfterElement(tbody, e2.clientY);
				if (afterElement == null) {
					tbody.appendChild(dragRow);
				} else {
					tbody.insertBefore(dragRow, afterElement);
				}
			};

			var onMouseUp = function() {
				if (dragRow) {
					dragRow.style.opacity = '';
				}
				dragRow = null;
				document.removeEventListener('mousemove', onMouseMove);
				document.removeEventListener('mouseup', onMouseUp);
			};

			document.addEventListener('mousemove', onMouseMove);
			document.addEventListener('mouseup', onMouseUp);
		});

		function getDragAfterElement(container, y) {
			var elements = Array.prototype.slice.call(
				container.querySelectorAll('tr:not([style*="opacity"])')
			);
			var closest = null;
			var closestOffset = Number.NEGATIVE_INFINITY;

			elements.forEach(function(child) {
				var box = child.getBoundingClientRect();
				var offset = y - box.top - box.height / 2;
				if (offset < 0 && offset > closestOffset) {
					closestOffset = offset;
					closest = child;
				}
			});

			return closest;
		}
	}
};

document.addEventListener('DOMContentLoaded', function() {
	Fields.initialise();
});
