/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
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
