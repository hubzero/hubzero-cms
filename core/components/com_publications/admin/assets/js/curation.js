/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
if (!jq) {
	var jq = $;
}

HUB.PublicationsCuration = {
	jQuery: jq,
	
	initialize: function() 
	{
		var $ = this.jQuery;
		
		// Enable reordering
		HUB.PublicationsCuration.reorder($('#blockorder'));		
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
				HUB.PublicationsCuration.saveOrder();
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
				id = id.replace('s-', '');

				if (id != '' && id != ' ')
				{
					selections = selections + id + '-' ;
				}
			});
		}
		$('#neworder').val(selections);
	}
}

jQuery(document).ready(function($){
	HUB.PublicationsCuration.initialize();
});	
