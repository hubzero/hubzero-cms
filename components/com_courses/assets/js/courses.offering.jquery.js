/**
 * @package     hubzero-cms
 * @file        components/com_courses/assets/js/courses.offering.jquery.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq) {
	var $ = jq;
	
	$('.unit-content').on('click', 'h3', function(){
		if ($(this).hasClass('unit-content-available')) {
			$(this).siblings('.unit-availability').find('.details').slideUp(500);
			$(this).removeClass('unit-content-available');
		} else {
			$(this).siblings('.unit-availability').find('.details').slideDown(500);

			// Toggle class for arrow (active gives down arrow indicating expanded list)
			$(this).addClass('unit-content-available');
		}
	});
});