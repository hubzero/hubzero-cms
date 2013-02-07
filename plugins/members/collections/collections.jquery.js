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

;(function($, undefined) {
'use strict';

// blank image data-uri bypasses webkit log warning (thx doug jones)
var BLANK = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///ywAAAAAAQABAAACAUwAOw==';

$.fn.imagesLoaded = function( callback ) {
	var $this = this,
		deferred = $.isFunction($.Deferred) ? $.Deferred() : 0,
		hasNotify = $.isFunction(deferred.notify),
		$images = $this.find('img').add( $this.filter('img') ),
		loaded = [],
		proper = [],
		broken = [];

	// Register deferred callbacks
	if ($.isPlainObject(callback)) {
		$.each(callback, function (key, value) {
			if (key === 'callback') {
				callback = value;
			} else if (deferred) {
				deferred[key](value);
			}
		});
	}

	function doneLoading() {
		var $proper = $(proper),
			$broken = $(broken);

		if ( deferred ) {
			if ( broken.length ) {
				deferred.reject( $images, $proper, $broken );
			} else {
				deferred.resolve( $images );
			}
		}

		if ( $.isFunction( callback ) ) {
			callback.call( $this, $images, $proper, $broken );
		}
	}

	function imgLoaded( img, isBroken ) {
		// don't proceed if BLANK image, or image is already loaded
		if ( img.src === BLANK || $.inArray( img, loaded ) !== -1 ) {
			return;
		}

		// store element in loaded images array
		loaded.push( img );

		// keep track of broken and properly loaded images
		if ( isBroken ) {
			broken.push( img );
		} else {
			proper.push( img );
		}

		// cache image and its state for future calls
		$.data( img, 'imagesLoaded', { isBroken: isBroken, src: img.src } );

		// trigger deferred progress method if present
		if ( hasNotify ) {
			deferred.notifyWith( $(img), [ isBroken, $images, $(proper), $(broken) ] );
		}

		// call doneLoading and clean listeners if all images are loaded
		if ( $images.length === loaded.length ){
			setTimeout( doneLoading );
			$images.unbind( '.imagesLoaded' );
		}
	}

	// if no images, trigger immediately
	if ( !$images.length ) {
		doneLoading();
	} else {
		$images.bind( 'load.imagesLoaded error.imagesLoaded', function( event ){
			// trigger imgLoaded
			imgLoaded( event.target, event.type === 'error' );
		}).each( function( i, el ) {
			var src = el.src;

			// find out if this image has been already checked for status
			// if it was, and src has not changed, call imgLoaded on it
			var cached = $.data( el, 'imagesLoaded' );
			if ( cached && cached.src === src ) {
				imgLoaded( el, cached.isBroken );
				return;
			}

			// if complete is true and browser supports natural sizes, try
			// to check for image status manually
			if ( el.complete && el.naturalWidth !== undefined ) {
				imgLoaded( el, el.naturalWidth === 0 || el.naturalHeight === 0 );
				return;
			}

			// cached images don't fire load sometimes, so we reset src, but only when
			// dealing with IE, or image is complete (loaded) and failed manual check
			// webkit hack from http://groups.google.com/group/jquery-dev/browse_thread/thread/eee6ab7b2da50e1f
			if ( el.readyState || el.complete ) {
				el.src = BLANK;
				el.src = src;
			}
		});
	}

	return deferred ? deferred.promise( $this ) : $this;
};

})(jQuery);

//----------------------------------------------------------
// Resource Ranking pop-ups
//----------------------------------------------------------
HUB.Plugins.GroupsBulletinboard = {
	jQuery: jq,
	
	initialize: function() {
		var $ = this.jQuery;

		/*if ($('#boards').length > 0) {
			$('#boards').masonry({
				itemSelector: '.post'
			});
		}*/

		if ($('#posts').length > 0) {
			//$('#bulletins').imagesLoaded(function(){
				$('#posts').masonry({
					itemSelector: '.post'/*,
					columnWidth: function(containerWidth) {
						return containerWidth / 3;
					}*/
				});
			//});
			$('#posts a.vote').each(function(i, el){
				$(el).on('click', function(e){
					e.preventDefault();

					href = $(this).attr('href');
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);

					$.get($(this).attr('href'), {}, function(data){
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
					if (href.indexOf('?') == -1) {
						href += '?no_html=1';
					} else {
						href += '&no_html=1';
					}
					$(this).attr('href', href);	
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
			
			$('#page_content a.follow, #page_content a.unfollow').on('click', function(e){
				e.preventDefault();

				var el = $(this);

				href = $(this).attr('href');
				if (href.indexOf('?') == -1) {
					href += '?no_html=1';
				} else {
					href += '&no_html=1';
				}
				$(this).attr('href', href);
				
				$.getJSON($(this).attr('href'), {}, function(data) {
					if (data.success) {
						//var unfollow = $(el).attr('data-href-unfollow');
						var follow = $(el).attr('data-text-follow'),
							unfollow = $(el).attr('data-text-unfollow');

						if ($(el).children('span').text() == follow) {
							$(el).removeClass('follow')
								.addClass('unfollow')
								.children('span')
								.text(unfollow)
								.attr('href', data.href);
						} else {
							$(el).removeClass('unfollow')
								.addClass('follow')
								.children('span')
								.text(follow)
								.attr('href', data.href);
						}
					}
				});
			});
		}
		
		HUB.Plugins.GroupsBulletinboard.formOptions(false);
		
		$('#hubForm .post-type a').each(function(i, el){
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
					HUB.Plugins.GroupsBulletinboard.formOptions(true);
				});
			});
		});
	}, // end initialize

	formOptions: function(initEditor) {
		var $ = this.jQuery;

		if (initEditor) {
			if (typeof(HUB.Plugins.WikiEditorToolbar) != 'undefined') {
				HUB.Plugins.WikiEditorToolbar.initialize();
			}
		}

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

		$('.file-add a').each(function(i, el){
			$(el).on('click', function(e){
				e.preventDefault();

				var prev = $($(this).parent()).prev();
				var clone = prev.clone();
				clone.find('input').val('');
				prev.after(clone);
			});
		});
	}
}

jQuery(document).ready(function($){
	HUB.Plugins.GroupsBulletinboard.initialize();
});
