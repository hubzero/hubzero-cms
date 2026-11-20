/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
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
		if ($(item).attr('formaction')) {
			$(item).on('click', function (e) {
				e.preventDefault();

				$.post($(this).attr('formaction').nohtml(), $(this.form).serialize(), function(data) {
					$('.tooltip').hide();
					$(item).closest('.voting').html(data);
				});
			});
		}
	});
});
