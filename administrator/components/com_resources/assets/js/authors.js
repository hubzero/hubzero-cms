/**
 * @package     hubzero-cms
 * @file        administrator/components/com_resources/resources.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (typeof(HUB) === 'undefined') {
	var HUB = {};
}

HUB.Resources = {
	removeAuthor: function(el) {
		var $ = jq;

		$('#' + el).remove();

		HUB.Resources.serialize();

		return false;
	},
	
	serialize: function() {
		var $ = jq,
			col = [];

		$('#author-list').find('li').each(function(i, elm) {
			col.push($(elm).attr('id').split('_')[1]);
		});
		$('#new_authors').val(col.join(','));
	},

	addAuthor: function() {
		var $ = jq,
			authid = $('#authid'),
			authorlist = $('#author-list');

		if (!authid.length) {
			alert('Author select not found');
			return;
		}
		if (!authorlist.length) {
			alert('Author list not found');
			return;
		}
		if (!authid.val()) {
			alert('No author provided');
			return;
		}

		var selectedRole = $('#authrole').val();
		var selectedId = authid.val();

		$.get('index.php?option=com_resources&controller=items&task=author&no_html=1&u='+selectedId+'&role='+selectedRole+'&rid='+$('#id').val(),{}, function (response) {
			var col = [];

			// re-apply the sorting script so the new LIst item becoems sortable
			authorlist.append(response);
			authorlist.sortable('enable');

			// get the new serials
			HUB.Resources.serialize();
		});
	}
};

jQuery(document).ready(function(jq){
	var $ = jq;

	$('#author-list').sortable({
		handle: 'span.handle',
		placeholder: 'author-placeholder',
		forcePlaceholderSize: true,
		start: function (e, ui) {
			$(ui.helper).addClass('dragging');
		},
		stop: function (e, ui) {
			$(ui.item).css({width:''}).removeClass('dragging');
		},
		update: function (e, ui) {
			HUB.Resources.serialize();
		}
	});
});

