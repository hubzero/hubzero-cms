/**
 * @package     hubzero-cms
 * @file        plugins/groups/announcements/announcements.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	// Close Announcement
	$('.announcement')
		.on('click', 'a.close', function(event) {
			event.preventDefault();

			var announcement = $(this).parents('.announcement-container'),
				id = $(this).attr('data-id'),
				days = $(this).attr('data-duration');

			//create cookie exirpation date
			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));

			//hide announcement
			announcement
				.animate({
					height: '0'
				}, 500, function(){
					$(this).remove();
				});

			//set cookie
			document.cookie = 'group_announcement_' + id + '=closed; expires=' + date.toGMTString() + ';';
		})
		.on('click', 'a.delete', function(event) {
			if (confirm($(this).attr('data-confirm'))) {
				return true;
			}
			event.preventDefault();
			return false;
		});

	//date/time picker for publish up/down
	if ($('.datepicker').length && jQuery.datetimepicker) {
		$('.datepicker').attr('autocomplete', 'OFF');
		$('.datepicker').datetimepicker({
			controlType: 'slider',
			dateFormat: 'mm/dd/yy',
			timeFormat: '@ h:mm tt'
		});
	}
});
