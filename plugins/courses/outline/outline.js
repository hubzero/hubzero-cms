/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/outline.js
 * @copyright   Copyright 2005-2014 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	$('.asset-primary').on('click', function(){
		var el = $($(this).parent());
		if (el.hasClass('collapsed')) {
			el.removeClass('collapsed');
		} else {
			el.addClass('collapsed');
		}
	});

	$('.advertise-popup').fancybox({
		type: 'iframe',
		height:($(window).height())*5/6,
		autoSize: false
	});
});