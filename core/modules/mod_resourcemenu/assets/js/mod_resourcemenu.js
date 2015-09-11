/**
 * @package     hubzero-cms
 * @file        modules/mod_resourcemenu/assets/js/mod_resourcemenu.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq,
		nav = $('#nav'),  // find the main navigation
		popup = $('#resources-menu');  // find the popup's content

	if (nav.length && popup.length) {
		var rnav = null;

		// find the "Resources" link
		var triggers = nav.getElementsByTagName('a');
		for (i = 0; i < triggers.length; i++) {
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
			var h = nav.offset().top;
			popup.css('top', (h + 26) +'px');
			// remove the popup and reattach it to the nav item
			// this is done to make the popup contents clickable 
			// otherwise it would disappear as soon as the
			// cursor moved away from "resources/"

			//document.body.removeChild(popup);
			var bdy = popup.parentNode;
			bdy.removeChild(popup);
			rnav.appendChild(popup);

			rnav
				.on('mouseover', function() { 
					var z = HUB.Position.findPosY(nav);
					if (z != h) {
						popup.css('top', (z + 26) +'px');
					}
					popup.removeClass('off'); 
				})
				.on('mouseout', function() {
					popup.addClass('off');
				});
		}
	}
});
