/**
 * @package     hubzero-cms
 * @file        components/com_forum/assets/js/forum.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

jQuery(document).ready(function (jq) {
	var $ = jq;

	$('a.delete').on('click', function (e) {
		var res = confirm('Are you sure you wish to delete this item?');
		if (!res) {
			e.preventDefault();
		}
		return res;
	});
	$('a.reply').on('click', function (e) {
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
	});
});
