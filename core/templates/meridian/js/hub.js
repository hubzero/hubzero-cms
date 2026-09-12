//  Create our namespace
if (typeof HUB === "undefined") {
	var HUB = {};
}
HUB.Base = {};
HUB.template = {};

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
jQuery(document).ready(function(jq) {
	var $ = jq,
		w = 760,
		h = 520,
		templatepath = '/app/templates/meridian/';

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
				href: templatepath + 'images/anim/loading.gif'
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

	// ******************************************************************************************************
	// Template
	// ******************************************************************************************************
	CORE.setupPage();
});
