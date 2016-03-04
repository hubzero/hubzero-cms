/**
 * @package     hubzero-cms
 * @file        plugins/groups/collections/collections.js
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
var _DEBUG = 0;

jQuery(document).ready(function(jq){
	var $ = jq,
		container = $('#posts'),
		isActive = true;

	_DEBUG = $('#system-debug').length;

	if (container.length > 0) {
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

		var opts = $('.view-options a');
		opts.each(function(i, el) {
			if ($(this).hasClass('selected') && $(this).hasClass('icon-list')) {
				isActive = false;
			}
		});
		/*opts.on('click', function (e) {
			e.preventDefault();

			opts.each(function (i, el) {
				container.removeClass($(el).attr('data-view'));
				$(el).removeClass('selected');
			});

			container.addClass($(this).attr('data-view'));

			$(this).addClass('selected');

			if (isActive) {
				container.masonry('destroy');
			} else {
				container.masonry({
					itemSelector: '.post'
				});
			}

			isActive = !isActive;
		});*/

		// Masonry
		if (isActive) {
			container.masonry({
				itemSelector: '.post'
			});
		}

		if (jQuery.ui && jQuery.ui.sortable) {
			$('#posts').sortable({
				handle: '.sort-handle',
				items: "div.post:not(.new-post)",
				update: function (e, ui) {
					var col = $("#posts").sortable("serialize");

					if (_DEBUG) {
						window.console && console.log('Calling: ' + $('#posts').attr('data-update').nohtml() + '&' + col);
					}

					$.getJSON($('#posts').attr('data-update').nohtml() + '&' + col, function(response) {
						if (_DEBUG) {
							window.console && console.log(response);
						}
					});
				}
			});
		}

		// Infinite scroll
		container.infinitescroll({
				navSelector  : '.list-footer',    // selector for the paged navigation
				nextSelector : '.list-footer .next a',  // selector for the NEXT link (to page 2)
				itemSelector : '#posts div.post',     // selector for all items you'll retrieve
				loading: {
					finishedMsg: 'No more pages to load.',
					img: container.attr('data-base') + '/core/components/com_collections/assets/img/spinner.gif'
				},
				path: function(index) {
					var path = $('.list-footer .next a').attr('href');
					limit = path.match(/limit[-=]([0-9]*)/).slice(1);
					start = path.match(/start[-=]([0-9]*)/).slice(1);
					//console.log(path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit)));
					return path.replace(/start[-=]([0-9]*)/, 'no_html=1&start=' + (limit * index - limit));
				},
				debug: false
			},
			// trigger Masonry as a callback
			function(newElements) {
				// hide new items while they are loading
				var $newElems = $(newElements).css({ opacity: 0 });

				// show elems now they're ready
				$newElems.animate({ opacity: 1 });
				container.masonry('appended', $newElems, true);
			}
		);

		if (container.hasClass('loggedin')) {
			container
				.find('a.vote')
				.on('click', function(e){
					e.preventDefault();

					var el = $(this);

					$.get(el.attr('href').nohtml(), {}, function(data){
						var like = el.attr('data-text-like'),
							unlike = el.attr('data-text-unlike')

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

						$('#post_' + el.attr('data-id') + ' .likes').text(data);
					});
				});

			$('#collections a.repost').fancybox({
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

			container.find('a.delete').fancybox({
				type: 'ajax',
				width: 300,
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
				afterShow: function() {
					var el = this.element;
					if ($('#hubForm').length) {
						$('#hubForm').on('submit', function(e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function(data) {
								$.fancybox.close();
								if (data)
								{
									window.location = data;
								}
							});
						});
					}
				}
			});
		}

		container.find('a.comment').fancybox({
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
	}

	$('#collections a.follow, #collections a.unfollow').on('click', function(e){
		e.preventDefault();

		var el = $(this);

		$.getJSON(el.attr('href').nohtml(), {}, function(data) {
			if (data.success) {
				//var unfollow = $(el).attr('data-href-unfollow');
				var follow = el.attr('data-text-follow'),
					unfollow = el.attr('data-text-unfollow');

				if (el.children('span').text() == follow) {
					el.removeClass('follow')
						.addClass('unfollow')
						.attr('href', data.href)
						.children('span')
						.text(unfollow);
					if (el.hasClass('icon-follow')) {
						el.removeClass('icon-follow')
							.addClass('icon-unfollow');
					}
				} else {
					el.removeClass('unfollow')
						.addClass('follow')
						.attr('href', data.href)
						.children('span')
						.text(follow);
					if (el.hasClass('icon-unfollow')) {
						el.removeClass('icon-unfollow')
							.addClass('icon-follow');
					}
				}
			}
		});
	});

	$("#ajax-uploader-list").sortable({
		handle: '.asset-handle'
	});
});
