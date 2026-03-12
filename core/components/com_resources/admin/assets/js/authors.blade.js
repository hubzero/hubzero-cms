/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

HUB.Resources = {
	removeAuthor: function(el) {
		var elem = document.getElementById(el);
		if (elem) {
			elem.parentNode.removeChild(elem);
		}

		HUB.Resources.serialize();

		return false;
	},

	serialize: function() {
		var col = [];
		var items = document.querySelectorAll('#author-list li');

		items.forEach(function(elm) {
			col.push(elm.getAttribute('id').split('_')[1]);
		});

		var input = document.getElementById('new_authors');
		if (input) {
			input.value = col.join(',');
		}
	},

	addAuthor: function() {
		var authid = document.getElementById('authid');
		var authorlist = document.getElementById('author-list');

		if (!authid) {
			alert('Author select not found');
			return;
		}
		if (!authorlist) {
			alert('Author list not found');
			return;
		}
		if (!authid.value) {
			alert('No author provided');
			return;
		}

		var selectedRole = '';
		var roleEl = document.getElementById('authrole');
		if (roleEl) {
			selectedRole = roleEl.value;
		}
		var selectedId = authid.value;
		var ridEl = document.getElementById('id');
		var rid = ridEl ? ridEl.value : '';

		fetch('index.php?option=com_resources&controller=items&task=author&no_html=1&u=' + selectedId + '&role=' + selectedRole + '&rid=' + rid)
			.then(function(response) { return response.text(); })
			.then(function(html) {
				authorlist.insertAdjacentHTML('beforeend', html);

				// Make new items draggable
				var newItems = authorlist.querySelectorAll('li:not([draggable])');
				newItems.forEach(function(item) {
					item.setAttribute('draggable', 'true');
				});

				HUB.Resources.serialize();
			});
	}
};

document.addEventListener('DOMContentLoaded', function() {
	var authorList = document.getElementById('author-list');

	if (authorList) {
		var dragItem = null;

		authorList.addEventListener('dragstart', function(e) {
			var handle = e.target.closest('span.handle');
			var li = e.target.closest('li');
			if (!handle || !li) {
				e.preventDefault();
				return;
			}
			dragItem = li;
			dragItem.classList.add('dragging');
			e.dataTransfer.effectAllowed = 'move';
			e.dataTransfer.setData('text/plain', '');
		});

		authorList.addEventListener('dragover', function(e) {
			e.preventDefault();
			e.dataTransfer.dropEffect = 'move';
			var target = e.target.closest('li');
			if (target && target !== dragItem && target.parentNode === authorList) {
				var rect = target.getBoundingClientRect();
				var mid = rect.top + rect.height / 2;
				if (e.clientY < mid) {
					authorList.insertBefore(dragItem, target);
				} else {
					authorList.insertBefore(dragItem, target.nextSibling);
				}
			}
		});

		authorList.addEventListener('dragend', function(e) {
			if (dragItem) {
				dragItem.classList.remove('dragging');
				dragItem.style.width = '';
				dragItem = null;
			}
			HUB.Resources.serialize();
		});

		// Make existing list items draggable
		var items = authorList.querySelectorAll('li');
		items.forEach(function(item) {
			item.setAttribute('draggable', 'true');
		});
	}
});
