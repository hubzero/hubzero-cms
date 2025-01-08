/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}


HUB.ResourcesACL = {
	removeUser: function(el) {
		var $ = jq;

		$('#' + el).remove();

		HUB.ResourcesACL.serialize_users();

		return false;
	},
	
	removeGroup: function(el) {
		var $ = jq;

		$('#' + el).remove();

		HUB.ResourcesACL.serialize_groups();

		return false;
	},

	serialize_users: function() {
		var $ = jq,
			col = [];

		$('#acluser-list').find('li').each(function(i, elm) {
			id = $(elm).attr('id').split('_')[1]

			if (jq.inArray(id, col) != -1) {
				$(elm).remove()
			} else {
				col.push(id)
			}
		});
		$('#new_aclusers').val(col.join(','));
	},

	serialize_groups: function() {
		var $ = jq,
			col = [];

		$('#aclgroup-list').find('li').each(function(i, elm) {
			id = $(elm).attr('id').split('_')[1]

			if (jq.inArray(id, col) != -1) {
				$(elm).remove()
			} else {
				col.push(id)
			}
		});
		$('#new_aclgroups').val(col.join(','));
	},

	addUser: function() {
		var $ = jq,
			acluserid = $('#acluserid'),
			acluserlist = $('#acluser-list');

		if (!acluserid.length) {
			alert('ACL User select not found');
			return;
		}
		if (!acluserlist.length) {
			alert('ACL User list not found');
			return;
		}
		if (!acluserid.val()) {
			alert('No ACL User provided');
			return;
		}

		var selectedId = acluserid.val();

		$.get('index.php?option=com_resources&controller=items&task=acluser&no_html=1&u='+selectedId+'&rid='+$('#id').val(),{}, function (response) {
			var col = [];

			// re-apply the sorting script so the new LIst item becoems sortable
			acluserlist.append(response);
			acluserlist.sortable('enable');

			// get the new serials
			HUB.ResourcesACL.serialize_users();
		});
	},

	addGroup: function() {
		var $ = jq,
			aclgroupid = $('#aclgroupid'),
			aclgrouplist = $('#aclgroup-list');

		if (!aclgroupid.length) {
			alert('ACL Group select not found');
			return;
		}
		if (!aclgrouplist.length) {
			alert('ACL Group list not found');
			return;
		}
		if (!aclgroupid.val()) {
			alert('No ACL Group provided');
			return;
		}

		var selectedId = aclgroupid.val();

		$.get('index.php?option=com_resources&controller=items&task=aclgroup&no_html=1&u='+selectedId+'&rid='+$('#id').val(),{}, function (response) {
			var col = [];

			// re-apply the sorting script so the new LIst item becoems sortable
			aclgrouplist.append(response);
			aclgrouplist.sortable('enable');

			// get the new serials
			HUB.ResourcesACL.serialize_groups();
		});
	}
};

jQuery(document).ready(function(jq){
	var $ = jq;

	$('#acluser-list').sortable({
		handle: 'span.handle',
		placeholder: 'acluser-placeholder',
		forcePlaceholderSize: true,
		start: function (e, ui) {
			$(ui.helper).addClass('dragging');
		},
		stop: function (e, ui) {
			$(ui.item).css({width:''}).removeClass('dragging');
		},
		update: function (e, ui) {
			HUB.ResourcesACL.serialize_users();
		}
	});

	$('#aclgroup-list').sortable({
		handle: 'span.handle',
		placeholder: 'aclgroup-placeholder',
		forcePlaceholderSize: true,
		start: function (e, ui) {
			$(ui.helper).addClass('dragging');
		},
		stop: function (e, ui) {
			$(ui.item).css({width:''}).removeClass('dragging');
		},
		update: function (e, ui) {
			HUB.ResourcesACL.serialize_groups();
		}
	});



});

