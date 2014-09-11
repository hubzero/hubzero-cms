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
if (!jq) {
	var jq = $;
}

HUB.Plugins.ResourcesShare = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;
		
		// Share links info pop-up
		var metadata = $('.metadata');
		var shareinfo = $('.shareinfo');
		if (shareinfo) {	
			$('.share').each(function(i, item) {
				$(item).bind('mouseover', function() {
					shareinfo.addClass('active');
				});
			});
			$('.share').each(function(i, item) {
				$(item).bind('mouseout', function() {
					shareinfo.removeClass('active');
				});
			});
		}
	} // end initialize
}

jQuery(document).ready(function($){
	HUB.Plugins.ResourcesShare.initialize();
});
