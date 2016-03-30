/**
 * @package     hubzero-cms
 * @file        plugins/members/blog/blog.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

jQuery(document).ready(function (jq) {
	var $ = jq;

	if ($("#field-publish_up").length && $("#field-publish_down").length) {
		$('#field-publish_up, #field-publish_down').datetimepicker({
			controlType: 'slider',
			dateFormat: 'yy-mm-dd',
			timeFormat: 'HH:mm:ss',
			timezone: $('#field-publish_up').attr('data-timezone')
		});
	}

	$('.below')
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
			var res = confirm($(this).attr('data-confirm'));
			if (!res) {
				e.preventDefault();
			}
			return res;
		});
});