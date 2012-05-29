/**
 * @package     hubzero-cms
 * @file        components/com_tags/tags.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
// Tags scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.Tags = {
	jQuery: jq,
	
	initialize: function()
	{
		//
		HUB.Tags.submitbutton();
		
		//
		HUB.Tags.deleteTag();
	},
	
	//-----
	
	submitbutton: function(pressbutton) {
		var $ = this.jQuery;
		
		var form = $('#hubForm');
		
		if(form.length)
		{
			if (pressbutton == 'cancel') {
				submitform( pressbutton );
				return;
			}

			// do field validation
			if (form.raw_tag.value == ''){
				alert( 'You must fill in a tag name' );
			} else {
				submitform( pressbutton );
			}
		}
	},
	
	//-----
	
	deleteTag: function()
	{
		var $ = this.jQuery;
		
		//add count to url
		$(".delete-tag").each(function(index) {
			var count = index + 1,
				url = $(this).attr("href");
			
			url += (url.indexOf("?") == -1) ? "?count="+count : "&count="+count;
			$(this).attr("href", url);
		});
		
		//do we need to scroll down
		if(window.location.hash)
		{
			var row_id = window.location.hash.replace("#count", ""),
				row = $($(".entries tr")[row_id]);
			
			$("body").animate({
				scrollTop: row.offset().top
			}, 500);
		}
	}
}

jQuery(document).ready(function($){
	HUB.Tags.initialize();
});
