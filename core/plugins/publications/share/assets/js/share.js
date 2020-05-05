/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
