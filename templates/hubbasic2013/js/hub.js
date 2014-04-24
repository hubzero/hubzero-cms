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

HUB.Base = {
	// Container for jquery. 
	// Needed for noconflict mode compatibility
	jQuery: jq,

	// Set the base path to this template
	templatepath: '/templates/hubbasic2013/',

	// launch functions
	initialize: function() {
		var $ = this.jQuery, w = 760, h = 520;

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

		// Set the overlay trigger for launch tool links
		$('.launchtool').on('click', function(e) {
			$.fancybox({
				closeBtn: false, 
				href: HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif'
			});
		});

		// Set overlays for lightboxed elements
		$('a[rel=lightbox]').fancybox();

		// Init tooltips
		$('.hasTip, .tooltips').tooltip({
			position: 'top center',
			effect: 'fade',
			offset: [-4, 0],
			onBeforeShow: function(event, position) {
				var tip = this.getTip(),
					tipText = tip[0].innerHTML;

				if (tipText.indexOf('::') != -1) {
					var parts = tipText.split('::');
					tip[0].innerHTML = '<span class="tooltip-title">' + parts[0] + '</span><span class="tooltip-text">' + parts[1] + '</span>';
				}
			}
		}).dynamic();

		// Init fixed position DOM: tooltips
		$('.fixedToolTip').tooltip({
			relative: true
		});
		
		HUB.Base.placeholderSupport();
	},
	
	placeholderSupport: function() {
		var $ = this.jQuery;

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
	}
};

jQuery(document).ready(function($){
	HUB.Base.initialize();
});

