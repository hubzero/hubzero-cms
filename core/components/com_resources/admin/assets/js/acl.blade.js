/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

HUB.ResourcesACL = {
	removeUser: function(el) {
		var elem = document.getElementById(el);
		if (elem) {
			elem.parentNode.removeChild(elem);
		}

		HUB.ResourcesACL.serialize_users();

		return false;
	},

	removeGroup: function(el) {
		var elem = document.getElementById(el);
		if (elem) {
			elem.parentNode.removeChild(elem);
		}

		HUB.ResourcesACL.serialize_groups();

		return false;
	},

	serialize_users: function() {
		var col = [];
		var items = document.querySelectorAll('#acluser-list li');

		items.forEach(function(elm) {
			var id = elm.getAttribute('id').split('_')[1];

			if (col.indexOf(id) !== -1) {
				elm.parentNode.removeChild(elm);
			} else {
				col.push(id);
			}
		});

		var input = document.getElementById('new_aclusers');
		if (input) {
			input.value = col.join(',');
		}
	},

	serialize_groups: function() {
		var col = [];
		var items = document.querySelectorAll('#aclgroup-list li');

		items.forEach(function(elm) {
			var id = elm.getAttribute('id').split('_')[1];

			if (col.indexOf(id) !== -1) {
				elm.parentNode.removeChild(elm);
			} else {
				col.push(id);
			}
		});

		var input = document.getElementById('new_aclgroups');
		if (input) {
			input.value = col.join(',');
		}
	},

	addUser: function() {
		var acluserid = document.getElementById('acluserid');
		var acluserlist = document.getElementById('acluser-list');

		if (!acluserid) {
			alert('ACL User select not found');
			return;
		}
		if (!acluserlist) {
			alert('ACL User list not found');
			return;
		}
		if (!acluserid.value) {
			alert('No ACL User provided');
			return;
		}

		var selectedId = acluserid.value;
		var ridEl = document.getElementById('id');
		var rid = ridEl ? ridEl.value : '';

		fetch('index.php?option=com_resources&controller=items&task=acluser&no_html=1&u=' + selectedId + '&rid=' + rid)
			.then(function(response) { return response.text(); })
			.then(function(html) {
				acluserlist.insertAdjacentHTML('beforeend', html);
				HUB.ResourcesACL.serialize_users();
			});
	},

	addGroup: function() {
		var aclgroupid = document.getElementById('aclgroupid');
		var aclgrouplist = document.getElementById('aclgroup-list');

		if (!aclgroupid) {
			alert('ACL Group select not found');
			return;
		}
		if (!aclgrouplist) {
			alert('ACL Group list not found');
			return;
		}
		if (!aclgroupid.value) {
			alert('No ACL Group provided');
			return;
		}

		var selectedId = aclgroupid.value;
		var ridEl = document.getElementById('id');
		var rid = ridEl ? ridEl.value : '';

		fetch('index.php?option=com_resources&controller=items&task=aclgroup&no_html=1&u=' + selectedId + '&rid=' + rid)
			.then(function(response) { return response.text(); })
			.then(function(html) {
				aclgrouplist.insertAdjacentHTML('beforeend', html);
				HUB.ResourcesACL.serialize_groups();
			});
	}
};

document.addEventListener('DOMContentLoaded', function() {
	var userList = document.getElementById('acluser-list');
	var groupList = document.getElementById('aclgroup-list');

	// HTML5 drag-and-drop sortable for user list
	if (userList) {
		HUB.ResourcesACL._initSortable(userList, function() {
			HUB.ResourcesACL.serialize_users();
		});
	}

	// HTML5 drag-and-drop sortable for group list
	if (groupList) {
		HUB.ResourcesACL._initSortable(groupList, function() {
			HUB.ResourcesACL.serialize_groups();
		});
	}
});

/**
 * Simple HTML5 drag-and-drop sortable for list items
 */
HUB.ResourcesACL._initSortable = function(list, onUpdate) {
	var dragItem = null;

	list.addEventListener('dragstart', function(e) {
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

	list.addEventListener('dragover', function(e) {
		e.preventDefault();
		e.dataTransfer.dropEffect = 'move';
		var target = e.target.closest('li');
		if (target && target !== dragItem && target.parentNode === list) {
			var rect = target.getBoundingClientRect();
			var mid = rect.top + rect.height / 2;
			if (e.clientY < mid) {
				list.insertBefore(dragItem, target);
			} else {
				list.insertBefore(dragItem, target.nextSibling);
			}
		}
	});

	list.addEventListener('dragend', function(e) {
		if (dragItem) {
			dragItem.classList.remove('dragging');
			dragItem.style.width = '';
			dragItem = null;
		}
		if (typeof onUpdate === 'function') {
			onUpdate();
		}
	});

	// Make list items draggable
	var items = list.querySelectorAll('li');
	items.forEach(function(item) {
		item.setAttribute('draggable', 'true');
	});
};
