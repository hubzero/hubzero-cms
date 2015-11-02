/**
 * @package     hubzero-cms
 * @file        components/com_collections/assets/js/collections.jquery.js
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
		.on('click', 'a.vote', function(e){
			e.preventDefault();

			var el = $(this);

			$.get(el.attr('href').nohtml(), {}, function(data){
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
		.on('click', 'a.follow, a.unfollow', function(e) {
			e.preventDefault();

			var el = $(this);

			$.getJSON(el.attr('href').nohtml(), {}, function(data) {
				if (data.success) {
					var follow = el.attr('data-text-follow'),
						unfollow = el.attr('data-text-unfollow');

					if (el.children('span').text() == follow) {
						el.removeClass('follow')
							.addClass('unfollow')
							.attr('href', data.href)
							.children('span')
							.text(unfollow);
					} else {
						el.removeClass('unfollow')
							.addClass('follow')
							.attr('href', data.href)
							.children('span')
							.text(follow);
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
			afterLoad: function(current, previous) {
				scrp = current.content.match(/<script type=\"text\/javascript\">(.*)<\/script>/ig);
				current.content = current.content.replace(/<script(.*)<\/script>/ig, '');
			},
			beforeShow: function() {
				if (scrp && scrp.length) {
					scrp = scrp[0].replace(/<script type=\"text\/javascript\">/ig, '').replace(/<\/script>/ig, '');
					eval(scrp);
				}
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
