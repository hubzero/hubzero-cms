/**
 * @package     hubzero-cms
 * @file        components/com_user/assets/js/link.jquery.js
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
// User link account js
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}

HUB.UserLink = {
	jQuery: jq,

	initialize: function() {
		var $ = HUB.UserLink.jQuery;

		// Initialize variables
		var option1   = $('#option1-link-existing');
		var option2   = $('#option2-create-new');
		var clickable = $('.clickable');
		var inner     = $('.inner');

		inner.hide();

		clickable.on('click', function(e){
			e.preventDefault();
			var next = $(this).next('.inner');
			if(!next.length) {
				next = $(this).parent().next('.inner');
			}
			next.slideToggle('fast');
		});
	},
}

jQuery(document).ready(function($){
	HUB.UserLink.initialize();
});