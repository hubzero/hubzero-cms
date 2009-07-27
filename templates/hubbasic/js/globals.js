/**
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
var HUB = {};

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

HUB.Base = {
	templatepath: '',
	
	//  Overlay for "loading", lightbox, etc.
	overlayer: function() {
		// The following code creates and inserts HTML into the document:
		// <div id="initializing" style="display:none;">
		//   <img id="loading" src="templates/zepar/images/circle_animation.gif" alt="" />
		// </div>

		$A(document.getElementsByTagName("script")).each( function(s) {
			if (s.src && s.src.match(/globals\.js(\?.*)?$/)) {
				HUB.Base.templatepath = s.src.replace(/js\/globals\.js(\?.*)?$/,'');
				imgpath = HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif';
			}
	    });

		var panel = new Element('div', {
			id: 'initializing',
			events: {
				'click': function(event) {
					this.setStyles({ display:'none' });
					$('sbox-overlay').setStyles({ display:'none', visibility: 'hidden', opacity: '0' });
				}
			}
		}).injectInside(document.body);
		
		var img = new Element('img', {
			id: 'loading',
			src: imgpath
		}).injectInside(panel);
		
		// Note: the rest of the code is in a separate function because it's needs to be
		// able to be called by itself (usually after loading some HTML via AJAX).
		HUB.Base.launchTool();
	},
	
	launchTool: function() {
		$$('.launchtool').each(function(trigger) {
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
	},

	// Amazon.com style popup menu
	azMenu: function() {
		var nav = $('nav');  // find the main navigation
		var popup = $('resources-menu');  // find the popup's content
		if (nav && popup) {
			var rnav = null;
			
			// find the "Resources" link
			var triggers = nav.getElementsByTagName('a');
			for (i = 0; i < triggers.length; i++) 
			{
				if (triggers[i].href.indexOf('resources/') != -1 
				 || triggers[i].href.indexOf('resources') != -1
				 || triggers[i].href.indexOf('/resources') != -1
				 || triggers[i].href.indexOf('/resources/') != -1) {
					rnav = triggers[i].parentNode;
					break;
				}
			}

			if (rnav) {
				// set the popup's position from the top of the page
				var h = HUB.Position.findPosY(nav);
				popup.style.top = (h + 36) +'px';
				// remove the popup and reattach it to the nav item
				// this is done to make the popup contents clickable 
				// otherwise it would disappear as soon as the
				// cursor moved away from "resources/"

				//document.body.removeChild(popup);
				var bdy = popup.parentNode;
				bdy.removeChild(popup);
				rnav.appendChild(popup);
				
				rnav.onmouseover = function() { 
					var z = HUB.Position.findPosY(nav);
					if (z != h) {
						popup.style.top = (z + 36) +'px';
					}
					popup.removeClass('off'); 
				};
				rnav.onmouseout = function() {
					popup.addClass('off');
				};
			}
		}
	},

	// set focus on username field for login form
	setLoginFocus: function() {
		if ($('username')) {
			$('username').focus();
		}
	},

	// turn links with specific classes into popups
	popups: function() {
		var w = 760;
		var h = 520;
		
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
	},

	menu: function() {
		$$('#nav li').each(function(li) {
			li.addEvent('mouseover', function(e) {
				this.addClass('sfhover');
			});
			li.addEvent('mouseout', function(e) {
				this.removeClass('sfhover');
			});
		});
	},

	// launch functions
	initialize: function() {
		HUB.Base.azMenu();
		HUB.Base.setLoginFocus();
		HUB.Base.popups();
		
		// Init SqueezeBox
		SqueezeBoxHub.initialize({ size: {x: 760, y: 520} });
		
		HUB.Base.overlayer();
		
		// Init Growl
		Growl.Bezel = new Gr0wl.Bezel(HUB.Base.templatepath+'images/bezel.png');
		Growl.Smoke = new Gr0wl.Smoke(HUB.Base.templatepath+'images/smoke.png');
		
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
