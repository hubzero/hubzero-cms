/**
 * @package     hubzero-cms
 * @file        templates/hubbasic2012/js/hub.js
 * @copyright   Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license     http://opensource.org/licenses/MIT MIT
 */

//  Create our namespace
if (typeof HUB === "undefined") {
	var HUB = {};
}
HUB.Base = {};

// Fallback support for browsers that don't have console.log
if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

// Support for jQuery noConflict mode
if (!jq) {
	var jq = $;
}

// Let's get down to business...
jQuery(document).ready(function(jq){
	var $ = jq,
		w = 760,
		h = 520,
		templatepath = '/core/templates/hubbasic2012/';

	// Set focus on username field for login form
	if ($('#username').length > 0) {
		$('#username').focus();
	}

	// Set the search box's placeholder text color
	if ($('#searchword').length > 0) {
		$('#searchword')
			.css('color', '#777')
			.on('focus', function(){
				if ($(this).val() == 'Search') {
					$(this).val('').css('color', '#ddd');
				}
			})
			.on('blur', function(){
				if ($(this).val() == '' || $(this).val() == 'Search') {
					$(this).val('Search').css('color', '#777');
				}
			});
	}

	// Turn links with specific classes into popups
	$('a').each(function(i, trigger) {
		if ($(trigger).is('.demo, .popinfo, .popup, .breeze')) {
			$(trigger).on('click', function (e) {
				e.preventDefault();

				if ($(this).attr('class')) {
					var sizeString = $(this).attr('class').split(' ').pop();
					if (sizeString && sizeString.match(/\d+x\d+/gi)) {
						var sizeTokens = sizeString.split('x');
						w = parseInt(sizeTokens[0]);
						h = parseInt(sizeTokens[1]);
					} else if (sizeString && sizeString == 'fullxfull') {
						w = screen.width;
						h = screen.height;
					}
				}

				window.open($(this).attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
			});
		}
		if ($(trigger).attr('rel') && $(trigger).attr('rel').indexOf('external') !=- 1) {
			$(trigger).attr('target', '_blank');
		}
	});

	if (jQuery.fancybox) {
		// Set the overlay trigger for launch tool links
		$('.launchtool').on('click', function(e) {
			$.fancybox({
				closeBtn: false, 
				href: HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif'
			});
		});

		// Set overlays for lightboxed elements
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
			content: function () {
				return $(this).attr('title');
			},
			create: function(event, ui) {
				var tip = $(this),
					tipText = tip.attr('title');

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip.attr('title', parts[1]);
				}
			},
			tooltipClass: 'tooltip'
		});

		// Init fixed position DOM: tooltips
		$('.fixedToolTip').tooltip({
			relative: true
		});
	}

	//test for placeholder support
	var test = document.createElement('input'),
		placeholder_supported = ('placeholder' in test);

	//if we dont have placeholder support mimic it with focus and blur events
	if (!placeholder_supported) {
		$('input[type=text]:not(.no-legacy-placeholder-support)').each(function(i, el) {
			var placeholderText = $(el).attr('placeholder');

			//make sure we have placeholder text
			if (placeholderText != '' && placeholderText != null) {
				//add plceholder text and class
				if ($(el).val() == '') {
					$(el).addClass('placeholder-support').val(placeholderText);
				}

				//attach event listeners to input
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
