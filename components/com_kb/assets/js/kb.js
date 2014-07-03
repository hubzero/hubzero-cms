/**
 * @package     hubzero-cms
 * @file        components/com_kb/assets/js/kb.jquery.js
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

	$('a.abuse').fancybox({
		type: 'ajax',
		width: 500,
		height: 'auto',
		autoSize: false,
		fitToView: false,
		titleShow: false,
		tpl: {
			wrap:'<div class="fancybox-wrap"><div class="fancybox-skin"><div class="fancybox-outer"><div id="sbox-content" class="fancybox-inner"></div></div></div></div>'
		},
		beforeLoad: function() {
			href = $(this).attr('href');
			$(this).attr('href', href.nohtml());
		},
		afterShow: function() {
			var frm = $('#hubForm-ajax'),
				self = $(this.element[0]);

			if (frm.length) {
				frm.on('submit', function(e) {
					e.preventDefault();
					$.post($(this).attr('action'), $(this).serialize(), function(data) {
						var response = jQuery.parseJSON(data);

						if (!response.success) {
							frm.prepend('<p class="error">' + response.message + '</p>');
							return;
						} else {
							$('#sbox-content').html('<p class="passed">' + response.message + '</p>');
							$('#c' + response.id)
								.find('.comment-body')
								.first()
								.html('<p class="warning">' + self.attr('data-txt-flagged') + '</p>');
						}

						setTimeout(function(){
							$.fancybox.close();
						}, 2 * 1000);
					});
				});
			}
		}
	});
});


