/**
 * @package     hubzero-cms
 * @file        templates/hubbasic/js/globals.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Extend Element to add some of our own functionality
//-----------------------------------------------------------
Element.extend({
	show: function() {
		this.style.display = '';
	},
	hide: function() {
		this.style.display = 'none';
	}
});

//-----------------------------------------------------------
//  Create our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-----------------------------------------------------------
//  Extend the Position object with a few helpful functions
//-----------------------------------------------------------
HUB.Position = {	
	findPosX: function(obj) {
		var curleft = 0;
		if(obj.offsetParent) {
			while (obj.offsetParent) {
				curleft += obj.offsetLeft;
				obj = obj.offsetParent;
			}
		} else if (obj.x) {
			curleft += obj.x;
		}
		return curleft;
	},
	
	findPosY: function(obj) {
		var curtop = 0;
		if(obj.offsetParent) {
			while (obj.offsetParent) {
				curtop += obj.offsetTop;
				obj = obj.offsetParent;
			}
		} else if (obj.y) {
			curtop += obj.y;
		}
		return curtop;
	}
};

//-----------------------------------------------------------
//  Various functions - encapsulated in HUB namespace
//-----------------------------------------------------------
HUB.Modules = {};
HUB.Components = {};
HUB.Base = {
	templatepath: '',

	// launch functions
	initialize: function() {
		var w = 760, h = 520;

		$A(document.getElementsByTagName("script")).each( function(s) {
			if (s.src && s.src.match(/globals\.js(\?.*)?$/)) {
				HUB.Base.templatepath = s.src.replace(/js\/globals\.js(\?.*)?$/,'');
			}
	    });

		// Set focus on username field for login form
		if ($('username')) {
			$('username').focus();
		}

		// Set the search box's placeholder text color
		if ($('searchword')) {
			$('searchword').addEvent('focus', function(){
				if (this.value == 'Search') {
					this.value = '';
					this.style.color = '#ddd';
				}
			});
			$('searchword').addEvent('blur', function(){
				if (this.value == '' || this.value == 'Search') {
					this.value = 'Search';
					this.style.color = '#777';
				}
			});
		}

		// Turn links with specific classes into popups
		$$('a').each(function(trigger) {
			if (trigger.hasClass('demo') 
			 || trigger.hasClass('popinfo') 
			 || trigger.hasClass('popup') 
			 || trigger.hasClass('breeze')) {
				trigger.addEvent('click', function(e) {
					new Event(e).stop();

					if (this.getProperty('className')) {
						var sizeString = this.getProperty('className').split(' ').pop();
						if (sizeString) {
							var sizeTokens = sizeString.split('x');
							w = parseInt(sizeTokens[0]);
							h = parseInt(sizeTokens[1]);
						}
					}
					
					window.open(this.getProperty('href'), 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w); 
				});
			}
			
			if (trigger.getProperty('rel') && trigger.getProperty('rel').indexOf('external') !=- 1) {
				trigger.setProperty('target','_blank');
			}
		});

		// Init SqueezeBox
		SqueezeBoxHub.initialize({ size: {x: 760, y: 520} });

		// Set overlays for lightboxed elements
		$$('a[rel=lightbox]').each(function(el) {
			el.addEvent('click', function(e) {
				new Event(e).stop();
				$(el).rel = '';
				SqueezeBoxHub.fromElement(el,{handler: 'image'});
			});
		});

		//HUB.Base.overlayer();
		$$('.launchtool').each(function(trigger) {
			if (!$('initializing')) {
				var panel = new Element('div', {
					id: 'initializing',
					styles: {
						'position': 'absolute',
						'margin-left': '-45px',
						'padding': '0',
						'top': '80px',
						'left': '50%',
						'width': '90px',
						'height': '90px',
						'z-index': '888',
						'text-align': 'center',
						'display': 'none'
					},
					events: {
						'click': function(event) {
							this.setStyles({ display:'none' });
							$('sbox-overlay').setStyles({ display:'none', visibility: 'hidden', opacity: '0' });
						}
					}
				}).injectInside(document.body);

				var img = new Element('img', {
					id: 'loading',
					src: HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif'
				}).injectInside(panel);
			}

			trigger.addEvent('click', function(e) {
				$('sbox-overlay').setStyles({
					width: window.getScrollWidth(), 
					height: window.getScrollHeight(), 
					display: 'block',
					visibility: 'visible',
					opacity: '0.7'
				});
				$('initializing').setStyles({
					top: (window.getScrollTop() + (window.getHeight() / 2) - 90), 
					display: 'block',
					zIndex: 65557
				});
			});
		});

		// Init tooltips
		var TTips = new Tips($$('.tooltips'));

		// Init fixed position DOM: tooltips
		var fTTips = new MooTips($$('.fixedToolTip'), {
			showDelay: 500,
			maxTitleChars: 100,
			fixed: true,
			offsets: {'x':20,'y':5}
		});
	}
};

//----------------------------------------------------------

window.addEvent('domready', HUB.Base.initialize);

