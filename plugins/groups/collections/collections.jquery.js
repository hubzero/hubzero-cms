/**
 * @package     hubzero-cms
 * @file        plugins/groups/blog/blog.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}
if (!HUB.Plugins) {
	HUB.Plugins = {};
}

if (!jq) {
	var jq = $;
}

String.prototype.nohtml = function () {
	if (this.indexOf('?') == -1) {
		return this + '?no_html=1';
	} else {
		return this + '&no_html=1';
	}
	//return this;
};

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.MembersCollections = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		var container = $('#posts');

		if (container.length > 0) {
			container.masonry({
				itemSelector: '.post'
			});

			container.infinitescroll({
					navSelector  : '.list-footer',    // selector for the paged navigation
					nextSelector : '.list-footer .next a',  // selector for the NEXT link (to page 2)
					itemSelector : '#posts div.post',     // selector for all items you'll retrieve
					loading: {
						finishedMsg: 'No more pages to load.',
						img: '/6RMhx.gif'
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
			
			$('#posts a.vote').each(function(i, el){
				$(el).on('click', function(e){
					e.preventDefault();

					$.get($(this).attr('href').nohtml(), {}, function(data){
						var like = $(el).attr('data-text-like');
						var unlike = $(el).attr('data-text-unlike');
						if ($(el).children('span').text() == like) {
							$(el).removeClass('like')
								.addClass('unlike')
								.children('span')
								.text(unlike);
						} else {
							$(el).removeClass('unlike')
								.addClass('like')
								.children('span')
								.text(unlike);
						}
						$('#b' + $(el).attr('data-id') + ' .likes').text(data);
					});
				});
			});
			
			$('#page_content a.repost').fancybox({
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
					var el = this.element;
					if ($('#hubForm')) {
						$('#hubForm').submit(function(e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function(data) {
								$('#b' + $(el).attr('data-id') + ' .reposts').text(data);
								$.fancybox.close();
							});
						});
					}
				}
			});

			/*$('#posts a.delete').fancybox({
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
					href = $(this).attr('href');

					$(this).attr('href', href.nohtml());
				},
				afterShow: function() {
					var el = this.element;
					if ($('#hubForm')) {
						$('#hubForm').submit(function(e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function(data) {
								$.fancybox.close();
								window.location = data;
							});
						});
					}
				}
			});*/

			$('#posts a.comment').fancybox({
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
					var el = this.element;
					if ($('#comment-form').length > 0) {
						//$('#comment-form').on('submit', function(e) {
						$('#sbox-content').on('submit', '#comment-form', function(e) {
							e.preventDefault();
							$.post($(this).attr('action'), $(this).serialize(), function(data) {
								//$('#b' + $(el).attr('data-id') + ' .reposts').text(data);
								//$.fancybox.close();
								$('#sbox-content').html(data);
								$.fancybox.update();
							});
						});
					}
				}
			});
		}
		
		$('#page_content a.follow, #page_content a.unfollow').on('click', function(e){
			e.preventDefault();

			var el = $(this);

			$.getJSON($(this).attr('href').nohtml(), {}, function(data) {
				if (data.success) {
					//var unfollow = $(el).attr('data-href-unfollow');
					var follow = $(el).attr('data-text-follow'),
						unfollow = $(el).attr('data-text-unfollow');

					if ($(el).children('span').text() == follow) {
						$(el).removeClass('follow')
							.addClass('unfollow')
							.attr('href', data.href)
							.children('span')
							.text(unfollow);
					} else {
						$(el).removeClass('unfollow')
							.addClass('follow')
							.attr('href', data.href)
							.children('span')
							.text(follow);
					}
				}
			});
		});
		
		HUB.Plugins.MembersCollections.formOptions(false);
		
		/*$('#hubForm .post-type a').each(function(i, el){
			$(el).on('click', function(e){
				e.preventDefault();
				//$('#hubForm .fieldset').addClass('hide');
				//$('#' + $(this).attr('rel')).removeClass('hide');

				$('.post-type a').removeClass('active');
				$(this).addClass('active');
				
				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
				
				$.get($(this).attr('href'), {}, function(data){
					$('#post-type-form').html(data);
					HUB.Plugins.MembersCollections.formOptions(true);
				});
			});
		});*/
		
		$("#ajax-uploader-list").sortable({
			handle: '.asset-handle'
		});
	}, // end initialize

	formOptions: function(initEditor) {
		var $ = this.jQuery;

		/*if (initEditor) {
			if (typeof(HUB.Plugins.WikiEditorToolbar) != 'undefined') {
				HUB.Plugins.WikiEditorToolbar.initialize();
			}
		}*/

		$('.toggle').each(function(i, el){
			$(el).on('click', function(e){
				e.preventDefault();

				var item = $('#' + $(this).attr('rel'));
				if (item.hasClass('hide')) {
					item.removeClass('hide');
					$(this).addClass('delete').removeClass('add');
					if ($(this).attr('data-text-hide')) {
						$(this).text($(this).attr('data-text-hide'));
					}
				} else {
					$(this).removeClass('delete').addClass('add');
					item.addClass('hide');
					if ($(this).attr('data-text-show')) {
						$(this).text($(this).attr('data-text-show'));
					}
				}
			});
		});

		/*$('.file-add a').each(function(i, el){
			$(el).on('click', function(e){
				e.preventDefault();

				var prev = $($(this).parent()).prev();
				var clone = prev.clone();
				clone.find('input').val('');
				prev.after(clone);
			});
		});*/
	}
}

jQuery(document).ready(function($){
	HUB.Plugins.MembersCollections.initialize();
});
