/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Project Publication Curation Manager JS
//----------------------------------------------------------

HUB.PublicationsCuration = {

	initialize: function() {
		// Enable reordering
		var blockorder = document.getElementById('blockorder');
		HUB.PublicationsCuration.reorder(blockorder);
	},

	reorder: function(list) {
		var reorderItems = document.querySelectorAll('.reorder');
		if (reorderItems.length == 0 || !list
			|| list.classList.contains('noedit')
		) {
			return false;
		}

		// Drag-and-drop sortable for list items
		var dragItem = null;

		list.addEventListener('mousedown', function(e) {
			var target = e.target.closest('li.reorder');
			if (!target) {
				return;
			}
			dragItem = target;
			dragItem.style.opacity = '0.5';

			var onMouseMove = function(e2) {
				e2.preventDefault();
				var afterElement = getDragAfterElement(list, e2.clientY);
				if (afterElement == null) {
					list.appendChild(dragItem);
				} else {
					list.insertBefore(dragItem, afterElement);
				}
			};

			var onMouseUp = function() {
				if (dragItem) {
					dragItem.style.opacity = '';
				}
				dragItem = null;
				document.removeEventListener('mousemove', onMouseMove);
				document.removeEventListener('mouseup', onMouseUp);
				HUB.PublicationsCuration.saveOrder();
			};

			document.addEventListener('mousemove', onMouseMove);
			document.addEventListener('mouseup', onMouseUp);
		});

		function getDragAfterElement(container, y) {
			var elements = Array.prototype.slice.call(
				container.querySelectorAll(
					'li.reorder:not([style*="opacity: 0.5"])'
				)
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
	},

	saveOrder: function() {
		var items = document.querySelectorAll('.pick');
		var selections = '';

		if (items.length > 0) {
			items.forEach(function(item) {
				var id = item.getAttribute('id');
				id = id.replace('s-', '');

				if (id != '' && id != ' ') {
					selections = selections + id + '-';
				}
			});
		}
		var neworder = document.getElementById('neworder');
		if (neworder) {
			neworder.value = selections;
		}
	}
};

document.addEventListener('DOMContentLoaded', function() {
	HUB.PublicationsCuration.initialize();
});
