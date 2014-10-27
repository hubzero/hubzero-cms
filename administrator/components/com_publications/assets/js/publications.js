/**
 * @package     hubzero-cms
 * @file        administrator/components/com_resources/resources.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

//----------------------------------------------------------
// Publication js
//----------------------------------------------------------

HUB.Publications = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		// Enable author reordering
		HUB.Publications.reorder($('#author-list'));	
	},
	
	reorder: function(list)
	{
		var $ = this.jQuery;

		if ($('.reorder').length == 0 || $(list).length == 0 || $(list).hasClass('noedit'))
		{
			return false;
		}

		// Drag items
		$(list).sortable(
		{
			items: "> li.reorder",
			update: function()
			{
				HUB.Publications.saveOrder();
		   	}
		});
	},

	saveOrder: function()
	{
		var $ = this.jQuery;
		var items = $('.pick');
		var selections = '';

		if (items.length > 0) 
		{
			items.each(function(i, item)
			{
				var id = $(item).attr('id');
				id = id.replace('author_', '');

				if (id != '' && id != ' ')
				{
					selections = selections + id + '-' ;
				}
			});
		}

		HUB.Publications.displayOrdering();
		$('#neworder').val(selections);
	},

	displayOrdering: function()
	{
		var $ = this.jQuery;
		var nums = $('.ordernum');
		var o	 = 1;

		if (nums.length > 0)
		{
			nums.each(function(i, item)
			{
				$(item).html(o);
				o++;
			});
		}
	}
};

jQuery(document).ready(function($){
	HUB.Publications.initialize();
});