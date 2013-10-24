/**
 * @package     hubzero-cms
 * @file        modules/mod_reportproblems/mod_reportproblems.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

//-------------------------------------------------------------
// Add an event to close the notice
//-------------------------------------------------------------
jQuery(document).ready(function(jq){
	var $ = jq;

	$('.modnotices .close').on('click', function(e) {
		e.preventDefault();

		var id = $($(this).parent().parent()).attr('id'),
			days = $(this).attr('data-duration');

		$($(this).parent().parent()).slideUp();

		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));

		document.cookie = id + '=closed; expires=' + date.toGMTString() + ';';
	});
});