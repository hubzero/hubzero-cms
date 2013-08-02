/**
 * @package     hubzero-cms
 * @file        components/com_blog/assets/js/blog.jquery.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('#content')
			// Toggle text and classes when clicking reply
			.on('click', 'a.reply', function (e) {
				e.preventDefault();

				var frm = $('#' + $(this).attr('rel'));

				if (frm.hasClass('hide')) {
					frm.removeClass('hide');

					$(this)
						.addClass('active')
						.text($(this).attr('data-txt-active'));
				} else {
					frm.addClass('hide');
					$(this)
						.removeClass('active')
						.text($(this).attr('data-txt-inactive'));
				}
			})
			// Add confirm dialog to delete links
			.on('click', 'a.delete', function (e) {
				var res = confirm('Are you sure you wish to delete this item?');
				if (!res) {
					e.preventDefault();
				}
				return res;
			});

	if ($('#hubForm').length > 0) {
		$('input.datetime-field').datetimepicker({  
				duration: '',
				showTime: true,
				constrainInput: false,
				stepMinutes: 1,
				stepHours: 1,
				altTimeField: '',
				time24h: true,
				dateFormat: 'yy-mm-dd',
				timeFormat: 'hh:mm:00'
			});
	}
});
