/**
 * @package     hubzero-cms
 * @file        plugins/resources/reviews/reviews.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	return this + (this.indexOf('?') == -1 ? '?' : '&') + 'no_html=1';
};

jQuery(document).ready(function(jq){
	var $ = jq;
		
	// Reply to review or comment
	$('a.reply').on('click', function (e) {
		e.preventDefault();

		var frm = $('#' + $(this).attr('data-rel'));

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

	$('.commentarea').on('focus', function(e) {
		if ($(this).val() == 'Enter your comments...') {
			$(this).val('');
		}
	});

	// review ratings
	$('#reviews-section').on('click', '.vote-button', function (e) {
		e.preventDefault();

		var item = $(this);

		if (!item.attr('href')) {
			return;
		}

		$.get(item.attr('href').nohtml(), {}, function(data) {
			item.closest('.voting').html(data);
			$('.tooltip').hide();
		});
	});
});
