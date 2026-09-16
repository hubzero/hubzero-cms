/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
	$('.inline-form:has(.vote-button)').on('submit', function(e) {
		e.preventDefault();

		var frm = $(this);

		$.post(frm.attr('action').nohtml(), frm.serialize(), function(data) {
			$('.tooltip').hide();
			frm.closest('.voting').html(data);
		});
	});
});
