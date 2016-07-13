/**
 * @package     hubzero-cms
 * @file        templates/kimera/js/hub.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

/*
USAGE:

	$.growl(title, msg);
	$.growl(title, msg, image);
	$.growl(title, msg, image, priority);

THEME/SKIN:

You can override the default look and feel by updating these objects:
$.growl.settings.displayTimeout = 4000;
$.growl.settings.noticeTemplate = ''
  + '<div>'
  + '  <img src="%image%" />'
  + '  <h3>%title%</h3>'
  + '  <p>%message%</p>'
  + '</div>';
$.growl.settings.noticeCss = {
  position: 'relative'
};

To change the 'dock' look, and position: 

$.growl.settings.dockTemplate = '<div></div>';
$.growl.settings.dockCss = {
    position: 'absolute',
    top: '10px',
    right: '10px',
    width: '300px'
  };
  
The dockCss will allow you to 'dock' the notifications to a specific area
on the page, such as TopRight (the default) or TopLeft, perhaps even in a
smaller area with "overflow: scroll" enabled?
*/

;(function($) {
	$.growl = function(title,message,image,priority) { notify(title,message,image,priority); }
	$.growl.version = "1.0.2";

	function create(rebuild) {
		var instance = document.getElementById('growlDock');
		if (!instance || rebuild) {
			instance = $(jQuery.growl.settings.dockTemplate).attr('id', 'growlDock').addClass('growl');
			if (jQuery.growl.settings.defaultStylesheet) {
				$('head').append('<link rel="stylesheet" type="text/css" href="' + jQuery.growl.settings.defaultStylesheet + '" />');
			}
		} else {
			instance = $(instance);
		}
		$('body').append(instance.css(jQuery.growl.settings.dockCss));
		return instance;
	};

	function r(text, expr, val) {
		while (expr.test(text)) {
			text = text.replace(expr, val);
		}
		return text;
	};

	function notify(title,message,image,priority) {
		var instance = create();
		var html = jQuery.growl.settings.noticeTemplate;
		if (typeof(html) == 'object') html = $(html).html();
		html = r(html, /%message%/, (message ? message : ''));
		html = r(html, /%title%/, (title ? title : ''));
		html = r(html, /%image%/, (image ? image : jQuery.growl.settings.defaultImage));
		html = r(html, /%priority%/, (priority ? priority : 'normal'));

		var notice = $(html)
			.hide()
			.css(jQuery.growl.settings.noticeCss)
			.fadeIn(jQuery.growl.settings.notice);

		$.growl.settings.noticeDisplay(notice);
		instance.append(notice);
		$('a[rel="close"]', notice).on('click', function(e) {
			e.preventDefault();
			notice.remove();
		});
		if ($.growl.settings.displayTimeout > 0) {
			$('a[rel="close"]', notice).hide();
			setTimeout(function(){
				jQuery.growl.settings.noticeRemove(notice, function(){
					notice.remove();
				});
			}, jQuery.growl.settings.displayTimeout);
		}
	};

	// default settings
	$.growl.settings = {
		dockTemplate: '<div></div>',
		dockCss: {
			position: 'fixed',
			top: '115px',
			right: '50%',
			//width: '30em',
			zIndex: 50000
		},
		noticeTemplate: 
			'<div class="growl-notice">' +
			'  %message%' + '<a href="#" class="growl-close" rel="close" title="Close">Close</a>' +
			'</div>',
		noticeCss: {
			opacity: 1, //.75,
			backgroundColor: 'transparent', //#333333',
			color: '#fff'
		},
		noticeDisplay: function(notice) {
			notice.css({'opacity':'0'}).fadeIn(jQuery.growl.settings.noticeFadeTimeout);
		},
		noticeRemove: function(notice, callback) {
			//notice.animate({opacity: '0', height: '0px'}, {duration:jQuery.growl.settings.noticeFadeTimeout, complete: callback});
			notice.fadeOut({duration:jQuery.growl.settings.noticeFadeTimeout, complete: callback});
		},
		noticeFadeTimeout: 'slow',
		displayTimeout: 2000,
		defaultImage: 'growl.png',
		defaultStylesheet: null,
		noticeElement: function(el) {
			$.growl.settings.noticeTemplate = $(el);
		}
	};
})(jQuery);

jQuery(document).ready(function($){
	var w = 760,
		h = 520,
		isHome = $('html').hasClass('home'),
		header = $('#masthead');

	// Javascript is enabled
	if ($('html').hasClass('no-js')) {
		$('html').removeClass('no-js');
	}

	// Make the header fixed
	$(window)
		.on('scroll', function (e) {
			var y = $(window).scrollTop();

			if (y >= 1) {
				header.addClass('fixed');
			} else {
				header.removeClass('fixed');
			}
		});

	// Adjust the top-padding
	if ($('#top').length) {
		$('#top').css('padding-top', header.height());
	}

	// Append a toggle (for smaller screens)
	header.find('li.parent>a').each(function (i, el) {
		$('<a class="toggle" href="#">&nbsp;</a>').on('click', function(e){
			e.preventDefault();

			$($(this).parent()).toggleClass('active');
		}).insertAfter($(el));
	});

	// Display system messages in Growl-like way
	var msg = $('#system-message-container');
	if (msg.length && msg.html().replace(/\s+/, '') != '') {
		$.growl.settings.displayTimeout = 0;
		$.growl('', msg.html());
	}

	// Turn links with specific classes into popups
	$('a').on('click', function (e) {
		var trigger = $(this);

		if (trigger.is('.demo, .popinfo, .popup, .breeze')) {
			e.preventDefault();

			if (trigger.attr('class')) {
				var sizeString = trigger.attr('class').split(' ').pop();
				if (sizeString && sizeString.match(/\d+x\d+/)) {
					var sizeTokens = sizeString.split('x');
					w = parseInt(sizeTokens[0]);
					h = parseInt(sizeTokens[1]);
				}
				else if (sizeString && sizeString == 'fullxfull')
				{
					w = screen.width;
					h = screen.height;
				}
			}

			window.open(trigger.attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
		}

		if (trigger.attr('rel') 
		 && trigger.attr('rel').indexOf('external') !=- 1) {
			trigger.attr('target', '_blank');
		}
	});

	// Set overlays for lightboxed elements
	if (jQuery.fancybox) {
		$('a[rel=lightbox]').fancybox();
	}

	// Init tooltips
	if (jQuery.ui && jQuery.ui.tooltip) {
		$('.hasTip, .tooltips').tooltip({
			position: {
				my: 'center bottom',
				at: 'center top'
			},
			// When moving between hovering over many elements quickly, the tooltip will jump around
			// because it can't start animating the fade in of the new tip until the old tip is
			// done. Solution is to disable one of the animations.
			hide: false,
			create: function(event, ui) {
				var tip = $(this),
					tipText = tip.attr('title');

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip.attr('title', parts[1]);
				}
			},
			content: function () {
				return $(this).attr('title');
			},
			tooltipClass: 'tooltip'
		});
	}

	// Test for placeholder support
	var test = document.createElement('input'),
		placeholder_supported = ('placeholder' in test);

	// If we dont have placeholder support mimic it with focus and blur events
	if (!placeholder_supported) {
		$('input[type=text]:not(.no-legacy-placeholder-support)').each(function(i, el) {
			var placeholderText = $(el).attr('placeholder');

			if (placeholderText != '' && placeholderText != null) {
				if ($(el).val() == '') {
					$(el).addClass('placeholder-support').val(placeholderText);
				}

				$(el)
					.on('focus', function() {
						if ($(el).val() == placeholderText) {
							$(el).removeClass('placeholder-support').val('');
						}
					})
					.on('blur', function(){
						if ($(el).val() == '') {
							$(el).addClass('placeholder-support').val(placeholderText);
						}
					});
			}
		});

		$('form').on('submit', function(event){
			$('.placeholder-support').each(function (i, el) {
				$(this).val('');
			});
		});
	}
});

