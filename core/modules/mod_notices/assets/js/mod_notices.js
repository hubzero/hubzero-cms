/**
 * @package     hubzero-cms
 * @file        modules/mod_notices/assets/js/mod_notices.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

//-------------------------------------------------------------
// Add an event to close the notice
//-------------------------------------------------------------
jQuery(document).ready(function(jq){
	var $ = jq;

	if (!$('html').hasClass('has-notice')) {
		$('html').addClass('has-notice');
	}

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