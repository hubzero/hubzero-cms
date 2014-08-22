/**
 * @package     hubzero-cms
 * @file        templates/hubbasic/js/globals.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
var HUB = HUB || {};
HUB.Base = {};

var alertFallback = true;
if (typeof console === "undefined" || typeof console.log === "undefined") {
	console = {};
	console.log = function() {};
}

//-----------------------------------------------------------
//  Various functions - encapsulated in HUB namespace
//-----------------------------------------------------------
if (!jq) {
	var jq = $;

	$.getDocHeight = function(){
		var D = document;
		return Math.max(Math.max(D.body.scrollHeight, D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
	};
} else {
	jq.getDocHeight = function(){
		var D = document;
		return Math.max(Math.max(D.body.scrollHeight, D.documentElement.scrollHeight), Math.max(D.body.offsetHeight, D.documentElement.offsetHeight), Math.max(D.body.clientHeight, D.documentElement.clientHeight));
	};
}

var template = {};

jQuery(document).ready(function(jq){
	var $ = jq,
		w = 760,
		h = 520,
		templatepath = '/templates/template/';

	// Set focus on username field for login form
	if ($('#username').length > 0) {
		$('#username').focus();
	}

	// Turn links with specific classes into popups
	$('a').each(function(i, trigger) {
		if ($(trigger).is('.demo, .popinfo, .popup, .breeze')) {
			$(trigger).on('click', function (e) {
				e.preventDefault();

				if ($(this).attr('class')) {
					var sizeString = $(this).attr('class').split(' ').pop();
					if (sizeString && sizeString.match(/\d+x\d+/)) {
						var sizeTokens = sizeString.split('x');
						w = parseInt(sizeTokens[0]);
						h = parseInt(sizeTokens[1]);
					}
					else if(sizeString && sizeString == 'fullxfull')
					{
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
				href: templatepath + 'images/anim/circling-ball-loading.gif'
			});
		});

		// Set overlays for lightboxed elements
		$('a[rel=lightbox]').fancybox();
	}

	// Init tooltips
	if (jQuery.ui && jQuery.ui.tooltip) {
		$(document).tooltip({
			items: '.hasTip, .tooltips',
			position: {
				my: 'center bottom',
				at: 'center top'
			},
			// When moving between hovering over many elements quickly, the tooltip will jump around
			// because it can't start animating the fade in of the new tip until the old tip is
			// done. Solution is to disable one of the animations.
			hide: false,
			content: function () {
				var tip = $(this),
					tipText = tip.attr('title');

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip.attr('title', parts[1]);
				}
				return $(this).attr('title');
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

	// Set the vars
	template.mobileNav = $('#mobile-nav');
	template.nav = $('nav#main-navigation');
	template.navIcon = $('#nav-icon');
	template.navHeight;
	template.windowState = template.mobileNav.css('display');

	$('#mobile-menu').on('click', function(e) {
		if (!(template.nav.hasClass('open'))) {
			template.nav.css('max-height', '1500px').addClass('open');
			template.navIcon.addClass('open');
		} else {
			template.nav.css('max-height', '0').removeClass('open');
			template.navIcon.removeClass('open');
		}

		e.preventDefault();	
	});

	$(window).resize(function() {
		if (!template.mobileNav) {
			return;
		}

		// Check if the state changed, do something then)
		if (template.mobileNav.css('display') != template.windowState) {
			// update window state to the current one
			template.windowState = template.mobileNav.css('display');

			if (template.mobileNav.css('display') == 'none') {
				template.nav.css('max-height', '');
			} else {
				template.nav.removeClass('open');
				template.navIcon.removeClass('open');
			}
		}
	});
});
