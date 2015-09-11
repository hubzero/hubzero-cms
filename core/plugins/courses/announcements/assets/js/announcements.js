/**
 * @package     hubzero-cms
 * @file        plugins/courses/announcements/assets/js/announcements.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function(jq){
	var $ = jq;

	$('.announcements .close').each(function(i, item) {
		$(item).on('click', function(e) {
			e.preventDefault();

			var id = $(this).attr('data-id'),
				days = $(this).attr('data-duration');

			$($(this).parent()).slideUp();

			var date = new Date();
			date.setTime(date.getTime()+(days*24*60*60*1000));

			document.cookie = 'ancmnt' + id + '=closed; expires=' + date.toGMTString() + ';';
		});
	});

	$('.announcement a.delete').each(function(i, el) {
		$(el).on('click', function(e) {
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
	});

	if ($('.datepicker').length) {
		$('.datepicker').datetimepicker({
			duration: '',
			showTime: true,
			constrainInput: false,
			stepMinutes: 1,
			stepHours: 1,
			altTimeField: '',
			time24h: true,
			timeFormat: 'HH:mm:00',
			dateFormat: 'yy-mm-dd'
		});
	}
});