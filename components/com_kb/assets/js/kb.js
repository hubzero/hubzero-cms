/**
 * @package     hubzero-cms
 * @file        components/com_kb/assets/js/kb.jquery.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//----------------------------------------------------------
// Registration form validation
//----------------------------------------------------------
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

	// Voting
	$('#content').on('click', '.vote-button', function (e) {
		if ($(this).attr('href')) {
			var el = $(this);
			e.preventDefault();

			$.get(el.attr('href').nohtml(), {}, function(data) {
				$(el.parent().parent()).html(data);
				$('.tooltip').hide();
			});
			return false;
		}
	});

	// Comment reply
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
});


