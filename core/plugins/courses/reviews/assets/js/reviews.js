/**
 * @package     hubzero-cms
 * @file        plugins/courses/reviews/assets/js/reviews.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
};

jQuery(document).ready(function(jq){
	var $ = jq;

	// Reply to review or comment
	$('.reply').on('click', function (e) {
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

	// review ratings
	$('.vote-button').each(function(i, item) {
		if ($(item).attr('href')) {
			$(item).on('click', function (e) {
				e.preventDefault();

				$.get($(this).attr('href').nohtml(), {}, function(data) {
					$('.tooltip').hide();
					$(item).closest('.voting').html(data);
				});
			});
		}
	});
});
