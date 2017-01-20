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
		container = $('#posts');

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

	if (container.hasClass('grid')) {
		// Masonry
		container.masonry({
			itemSelector: '.post'
		});
	}

	// Infinite scroll
	container.infinitescroll({
			navSelector  : '.list-footer',    // selector for the paged navigation
			nextSelector : '.list-footer .next a',  // selector for the NEXT link (to page 2)
			itemSelector : '#posts div.post',     // selector for all items you'll retrieve
			loading: {
				finishedMsg: 'No more pages to load.',
				img: container.attr('data-base') + '/core/components/com_collections/site/assets/img/spinner.gif'
			},
			path: function(index) {
				var path = $('.list-footer .next a').attr('href'),
					limit = $('#limit').val(),
					start = 0;
				if (path.match(/limit[-=]([0-9]*)/)) {
					limit = path.match(/limit[-=]([0-9]*)/).slice(1);
				}
				limit = limit ? limit : 25;
				start = path.match(/start[-=]([0-9]*)/).slice(1);
				return path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit));
			},
			debug: false
		},
		// Trigger Masonry as a callback
		function(newElements) {
			// Hide new items while they are loading
			var $newElems = $(newElements).css({ opacity: 0 });

			// Show elems now they're ready
			$newElems.animate({ opacity: 1 });
			container.masonry('appended', $newElems, true);
		});

	if (container.hasClass('loggedin')) {
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
	}

	// Add collect trigger
	container
		.find('a.comment')
		.fancybox({
			type: 'ajax',
			autoSize: false,
			fitToView: false,
			titleShow: false,
			autoCenter: false,
			width: '100%',
			height: 'auto',
			topRatio: 0,
			tpl: {
				wrap:'<div class="fancybox-wrap post-modal"><div class="fancybox-skin"><div class="fancybox-outer"><div id="post-content" class="fancybox-inner"></div></div></div></div>'
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
				if ($('#commentform').length > 0) {
					$('#post-content').on('submit', '#commentform', function(e) {
						e.preventDefault();
						$.post($(this).attr('action'), $(this).serialize(), function(data) {
							$('#post-content').html(data);
							$.fancybox.update();

							var metadata = $(el).parent().parent(); //$('#p' + $(el).attr('data-id')).find('.meta');
							if (metadata.length) {
								$.getJSON(metadata.attr('data-metadata-url').nohtml(), function(data) {
									metadata.find('.likes').text(data.likes);
									metadata.find('.comments').text(data.comments);
									metadata.find('.reposts').text(data.reposts);
								});
							}
						});
					});
				}
			},
			helpers: {
				overlay: {
					css: { background: 'rgba(200, 200, 200, 0.95)' }
				}
			}
		});
});

(function(d, s, id) {
  var js, fjs = d.getElementsByTagName(s)[0];
  if (d.getElementById(id)) return;
  js = d.createElement(s); js.id = id;
  js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.8&appId=500461250143859";
  fjs.parentNode.insertBefore(js, fjs);
}(document, 'script', 'facebook-jssdk'));
