/**
 * @package     hubzero-cms
 * @file        plugins/courses/outline/outline.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;

	$('ul.asset-list>li').on('click', function(){
		if ($(this).hasClass('collapsed')) {
			$(this).removeClass('collapsed');
		} else {
			$(this).addClass('collapsed');
		}
	});

	$('.advertise-popup').fancybox({
		type: 'iframe',
		height:($(window).height())*5/6,
		autoSize: false
	});
});