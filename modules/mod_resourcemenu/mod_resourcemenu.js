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
	var HUB = {
		Modules: {}
	};
}

//-------------------------------------------------------------
// ResourceMenu popup
//-------------------------------------------------------------

HUB.Modules.ResourceMenu = {
	// Amazon.com style popup menu
	initialize: function() {
		var nav = $('nav');  // find the main navigation
		var popup = $('resources-menu');  // find the popup's content
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
					rnav = triggers[i].parentNode;
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
				
				rnav.addEvent('mouseover', function() { 
					var z = HUB.Position.findPosY(nav);
					if (z != h) {
						popup.style.top = (z + 26) +'px';
					}
					popup.removeClass('off'); 
				});
				rnav.addEvent('mouseout', function() {
					popup.addClass('off');
				});
			}
		}
	}
};

//----------------------------------------------------------

window.addEvent('domready', HUB.Modules.ResourceMenu.initialize);
