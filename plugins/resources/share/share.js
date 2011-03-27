/**
 * @package     hubzero-cms
 * @file        plugins/resources/share/share.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.ResourcesShare = {
	initialize: function() {
		// Share links info pop-up
		var metadata = $$('.metadata');
		var shareinfo = $$('.shareinfo');
		if (shareinfo) {
			var ell = metadata.getElement('.shareinfo');		
			$$('.share').each(function(item) {
				item.addEvent('mouseover', function() {					
					ell.addClass('active');
				});
			});
			$$('.share').each(function(item) {
				item.addEvent('mouseout', function() {					
					ell.removeClass('active');
				});
			});
		}
	} // end initialize
}

window.addEvent('domready', HUB.Plugins.ResourcesShare.initialize);
