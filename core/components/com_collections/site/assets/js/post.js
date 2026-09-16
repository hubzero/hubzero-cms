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

var scrp = null;

jQuery(document).ready(function(jq){
	var $ = jq,
		container = $('#content');

	// Are there any posts?
	if (container.length <= 0) {
		return;
	}

	// Set overlays for lightboxed elements
	$('a.img-link').fancybox({
		afterLoad: function() {
			if ($(this.element).attr('data-download')) {
				this.title = '<a href="' + $(this.element).attr('data-download') + '" download="' + $(this.element).attr('data-download') + '">' + $(this.element).attr('data-downloadtext') + '</a> ' + this.title;
			}
		},
		helpers: {
			title: {
				type: 'inside'
			}
		}
	});

	// Add voting trigger
	container
		.on('submit', '.inline-form:has(.vote)', function(e){
			e.preventDefault();

			var frm = $(this),
				el = frm.find('.vote');

			$.post(frm.attr('action').nohtml(), frm.serialize(), function(data){
				var like = el.attr('data-text-like'),
					unlike = el.attr('data-text-unlike');

				if (el.children('span').text() == like) {
					el.removeClass('like')
						.addClass('unlike')
						.children('span')
						.text(unlike);
				} else {
					el.removeClass('unlike')
						.addClass('like')
						.children('span')
						.text(like);
				}

				$('#b' + el.attr('data-id') + ' .likes').text(data);
			});
		})
		.on('submit', '.inline-form:has(.follow, .unfollow)', function(e) {
			e.preventDefault();

			var frm = $(this),
				el = frm.find('.follow, .unfollow');

			$.post(frm.attr('action').nohtml(), frm.serialize(), function(data) {
				data = JSON.parse(data);
				if (data.success) {
					var follow = el.attr('data-text-follow'),
						unfollow = el.attr('data-text-unfollow');

					if (el.children('span').text() == follow) {
						el.removeClass('follow')
							.addClass('unfollow')
							.children('span')
							.text(unfollow);
						frm.attr('action', data.href);
					} else {
						el.removeClass('unfollow')
							.addClass('follow')
							.children('span')
							.text(follow);
						frm.attr('action', data.href);
					}
				}
			});
		});

	// Add collect trigger
	container
		.find('a.repost')
		.fancybox({
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
				$(this).attr('href', $(this).attr('href').nohtml());
			},
			beforeShow: function() {
				$(document).trigger('ajaxLoad');
			},
			afterShow: function() {
				var el = this.element;
				if ($('#hubForm')) {
					$('#hubForm').on('submit', function(e) {
						e.preventDefault();
						$.post($(this).attr('action'), $(this).serialize(), function(data) {
							$('#b' + $(el).attr('data-id') + ' .reposts').text(data);
							$.fancybox.close();
						});
					});
				}
			}
		});
});
