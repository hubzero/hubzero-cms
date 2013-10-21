/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/comments.comments.jquery.js
 * @copyright   Copyright 2005-2013 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
	var $ = jq,
		thread = $('div.thread');
		
	if (!thread.length) {
		return;
	}

	thread
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
		})// Add confirm dialog to delete links
		.on('click', 'a.delete', function (e) {
			var res = confirm('Are you sure you wish to delete this item?');
			if (!res) {
				e.preventDefault();
			}
			return res;
		})
		.on('click', 'a.vote-button', function(e) {
			e.preventDefault();

			var el = $(this);

			$.get(el.attr('href').nohtml(), {}, function(data) {
				$(el.parent().parent()).html(data);
			});
		});
});