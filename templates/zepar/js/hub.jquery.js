/**
 * @package     hubzero-cms
 * @file        templates/hubbasic/js/globals.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-----------------------------------------------------------
//  Various functions - encapsulated in HUB namespace
//-----------------------------------------------------------
HUB.Base = {
	
	jQuery: $,
	
	templatepath: '',

	// set focus on username field for login form
	setLoginFocus: function() {
		if ($('#username')) {
			$('#username').focus();
		}
	},

	// turn links with specific classes into popups
	popups: function() {
		var w = 760, h = 520;
		
		$('a').each(function(i, trigger) {
			if ($(trigger).is('.demo, .popinfo, .popup, .breeze')) {
				$(trigger).click(function (e) {
					e.preventDefault();
					
					if ($(this).attr('class')) {
						var sizeString = $(this).attr('class').split(' ').pop();
						if (sizeString && sizeString.match('/\d+x\d+/')) {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
					}
					
					window.open($(this).attr('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w);
				});
			}
			
			if ($(trigger).attr('rel') && $(trigger).attr('rel').indexOf('external') !=- 1) {
				$(trigger).attr('target','_blank');
			}
		});
	},

	searchbox: function() {
		if ($('#searchword')) {
			$('#searchword').css('color', '#999');
			$('#searchword').focus(function(){
				if ($(this).val() == 'Search') {
					$(this).val('');
					$(this).css('color', '#333');
				}
			});
			$('#searchword').blur(function(){
				if ($(this).val() == '' || $(this).val() == 'Search') {
					$(this).val('Search');
					$(this).css('color', '#999');
				}
			});
		}
	},

	// launch functions
	initialize: function() {
		HUB.Base.setLoginFocus();
		HUB.Base.searchbox();
		HUB.Base.popups();
		
		$('a[rel=lightbox]').fancybox({
		});
		
		// Init tooltips
		$('.hasTip').tooltip({
			position:'TOP RIGHT',
			//offset: [10,-20],
			onBeforeShow: function(event, position) {
				var tip = this.getTip(),
					tipText = tip[0].innerHTML;
					
				if (tipText.indexOf(" :: ") != -1) {
					var parts = tipText.split(" :: ");
					tip[0].innerHTML = "<span class=\"tooltip-title\">"+parts[0]+"</span><span>"+parts[1]+"</span>";
				}
			}
		}).dynamic({ bottom: { direction: 'down' }, right: { direction: 'left' } });
		$('.tooltips').tooltip({
			position:'TOP RIGHT',
			//offset: [10,2],
			onBeforeShow: function(event, position) {
				var tip = this.getTip(),
					tipText = tip[0].innerHTML;
					
				if (tipText.indexOf(" :: ") != -1) {
					var parts = tipText.split(" :: ");
					tip[0].innerHTML = "<span class=\"tooltip-title\">"+parts[0]+"</span><span>"+parts[1]+"</span>";
				}
			}
		}).dynamic({ bottom: { direction: 'down' }, right: { direction: 'left' } });
		
		// Init fixed position DOM: tooltips
		$('.fixedToolTip').tooltip({
			relative: true
		});
	}
	
};

jQuery(document).ready(function($){
	HUB.Base.initialize();
});

