/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
