/**
 * @package     hubzero-cms
 * @file        modules/mod_resourcemenu/mod_resourcemenu.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB || !HUB.Modules) {
	var HUB = {
	};
	HUB.Modules = {};
}

//-------------------------------------------------------------
// ResourceMenu popup
//-------------------------------------------------------------

if (!jq) {
	var jq = $;
}

HUB.Modules.ResourceMenu = {
	// Amazon.com style popup menu
	initialize: function() {
		var $ = this.jQuery;
		
		var nav = $('#nav');  // find the main navigation
		var popup = $('#resources-menu');  // find the popup's content
		if (nav && popup) {
			var rnav = null;
			
			// find the "Resources" link
			var triggers = nav.getElementsByTagName('a');
			for (i = 0; i < triggers.length; i++) 
			{
				if (triggers[i].href.indexOf('resources/') != -1 
				 || triggers[i].href.indexOf('resources') != -1
				 || triggers[i].href.indexOf('/resources') != -1
				 || triggers[i].href.indexOf('/resources/') != -1) {
					rnav = $(triggers[i].parentNode);
					break;
				}
			}

			if (rnav) {
				// set the popup's position from the top of the page
				var h = HUB.Position.findPosY(nav);
				popup.style.top = (h + 26) +'px';
				// remove the popup and reattach it to the nav item
				// this is done to make the popup contents clickable 
				// otherwise it would disappear as soon as the
				// cursor moved away from "resources/"

				//document.body.removeChild(popup);
				var bdy = popup.parentNode;
				bdy.removeChild(popup);
				rnav.appendChild(popup);
				
				rnav.ont('mouseover', function() { 
					var z = HUB.Position.findPosY(nav);
					if (z != h) {
						popup.style.top = (z + 26) +'px';
					}
					popup.removeClass('off'); 
				});
				rnav.on('mouseout', function() {
					popup.addClass('off');
				});
			}
		}
	}
};

jQuery(document).ready(function($){
	HUB.Modules.ResourceMenu.initialize();
});

