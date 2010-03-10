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
	//  Overlay for "loading", lightbox, etc.
	overlayer: function() {
		// the following code creates and inserts HTML into the document:
		// <div id="overlay" onclick="function(){...}">
		// </div>
		// <div id="initializing" style="display:none;">
		//   <img id="loading" src="templates/azure/images/circle_animation.gif" alt="" />
		// </div>

		HUB.Base.templatepath = '';
		$A(document.getElementsByTagName("script")).each( function(s) {
			if (s.src && s.src.match(/globals\.js(\?.*)?$/)) {
				HUB.Base.templatepath = s.src.replace(/js\/globals\.js(\?.*)?$/,'');
				imgpath = HUB.Base.templatepath + 'images/anim/circling-ball-loading.gif';
			}
	    });

		var panel = new Element('div', {'id':'initializing'});
		var img = new Element('img', {'id':'loading','src':imgpath}).injectInside(panel);
		var overlay = new Element('div', {'id': 'overlay'}).injectInside(document.body);
		panel.style.display = 'none';
		overlay.effect('opacity', {duration: 500}).hide();
		overlay.onclick = function() {
				panel.style.display = 'none';
				
				var fade = new Fx.Style(overlay, 'opacity').set(0);
			}
		
		document.body.appendChild(panel);
		
		HUB.Base.launchTool();
	},
	
	launchTool: function() {
		var panel = $('initializing');
		//var overlay = document.getElementById('overlay');
		var overlay = $('overlay');
		
		var triggers = document.getElements('.launchtool');
		triggers.each(function(trigger) {
			trigger.onclick = function() {
				overlay.setStyles({'width': window.getScrollWidth(), 'height': window.getScrollHeight()});
				
				panel.top = window.getScrollTop() + (window.getHeight() / 2) - 90;
				panel.setStyles({top: panel.top, display: ''});
				
				var fade = new Fx.Style(overlay, 'opacity', {onComplete:function(){panel.style.display = 'block';}}).set(0.8);
				panel.style.display = 'block';
			}
		});
	},

	// Amazon.com style popup menu
	azMenu: function() {
		var rnav = null;
		var nav = $('nav');  // find the main navigation
		var popup = $('resources-menu');  // find the popup's content
		if(nav && popup) {
			// find the "Resources" link
			var triggers = nav.getElementsByTagName('a');
			for (i = 0; i < triggers.length; i++) {
				if (triggers[i].href.indexOf('resources/') != -1 || triggers[i].href.indexOf('resources') != -1) {
					rnav = triggers[i].parentNode;
					break;
				}
			}

			if(rnav) {
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
					}
				rnav.onmouseout = function() { popup.addClass('off'); }
			}
		}
	},

	// set focus on username field for login form
	setLoginFocus: function() {
		if(document.login) {
			if(document.login.username) {
				document.login.username.focus();
			}
		}
	},

	// turn links with specific classes into popups
	popups: function() {
		var els = document.getElementsByTagName('a');

		if(els) {
			for(var i = 0; i < els.length; i++) 
			{
				if(Element.hasClass(els[i],'demo') ||
					Element.hasClass(els[i],'popinfo') ||
					Element.hasClass(els[i],'popup') || 
					Element.hasClass(els[i],'breeze')
				   ) {
					els[i].onclick=activate;
				}
				
				if(els[i].getAttribute('rel') && els[i].getAttribute('rel').indexOf('external') !=- 1) {
					els[i].setAttribute('target','_blank');
				}
			}
		}
	
		function activate() 
		{
			var w, h;
		
			if(this.className) {
				var classTokens = this.className.split(' ');
				var sizeString = classTokens.pop();
				if(sizeString) {
					var sizeTokens = sizeString.split('x');
					w = parseInt(sizeTokens[0]);
					h = parseInt(sizeTokens[1]);
				}
			}
		
			if(!w) { w = 760; }
			if(!h) { h = 520; }

			window.open(this.href, 'popup', 'resizable=1,scrollbars=1,height='+ h + ',width=' + w); 
		
			return false;
		}
	},

	// launch functions
	initialize: function() {
		HUB.Base.azMenu();
		HUB.Base.setLoginFocus();
		HUB.Base.overlayer();
		HUB.Base.popups();
	}
};

//----------------------------------------------------------

window.addEvent('domready', HUB.Base.initialize);
