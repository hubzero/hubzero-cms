/**
 * @package     hubzero-cms
 * @file        plugins/publications/share/share.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// Share links info pop-up
	var metadata = $('.metadata'),
		shareinfo = $('.shareinfo');

	if (shareinfo.length > 0) {
		$('.share')
			.on('mouseover', function() {
				shareinfo.addClass('active');
			})
			.on('mouseout', function() {
				shareinfo.removeClass('active');
			});
	}
});
