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
//  A clone of MooTools' Accordian class but with onmouseover
//  instead of onclick
//-----------------------------------------------------------
var Accordiono = Fx.Elements.extend({

	options: {
		onActive: Class.empty,
		onBackground: Class.empty,
		display: 0,
		show: false,
		height: true,
		width: false,
		opacity: true,
		fixedHeight: false,
		fixedWidth: false,
		wait: false,
		alwaysHide: false
	},

	initialize: function(){
		var options, togglers, elements, container;
		$each(arguments, function(argument, i){
			switch($type(argument)){
				case 'object': options = argument; break;
				case 'element': container = $(argument); break;
				default:
					var temp = $$(argument);
					if (!togglers) togglers = temp;
					else elements = temp;
			}
		});
		this.togglers = togglers || [];
		this.elements = elements || [];
		this.container = $(container);
		this.setOptions(options);
		this.previous = -1;
		if (this.options.alwaysHide) this.options.wait = true;
		if ($chk(this.options.show)){
			this.options.display = false;
			this.previous = this.options.show;
		}
		if (this.options.start){
			this.options.display = false;
			this.options.show = false;
		}
		this.effects = {};
		if (this.options.opacity) this.effects.opacity = 'fullOpacity';
		if (this.options.width) this.effects.width = this.options.fixedWidth ? 'fullWidth' : 'offsetWidth';
		if (this.options.height) this.effects.height = this.options.fixedHeight ? 'fullHeight' : 'scrollHeight';
		for (var i = 0, l = this.togglers.length; i < l; i++) this.addSection(this.togglers[i], this.elements[i]);
		this.elements.each(function(el, i){
			if (this.options.show === i){
				this.fireEvent('onActive', [this.togglers[i], el]);
			} else {
				for (var fx in this.effects) el.setStyle(fx, 0);
			}
		}, this);
		this.parent(this.elements);
		if ($chk(this.options.display)) this.display(this.options.display);
	},

	addSection: function(toggler, element, pos){
		toggler = $(toggler);
		element = $(element);
		var test = this.togglers.contains(toggler);
		var len = this.togglers.length;
		this.togglers.include(toggler);
		this.elements.include(element);
		if (len && (!test || pos)){
			pos = $pick(pos, len - 1);
			toggler.injectBefore(this.togglers[pos]);
			element.injectAfter(toggler);
		} else if (this.container && !test){
			toggler.inject(this.container);
			element.inject(this.container);
		}
		var idx = this.togglers.indexOf(toggler);
		toggler.addEvent('mouseover', this.display.bind(this, idx));
		if (this.options.height) element.setStyles({'padding-top': 0, 'border-top': 'none', 'padding-bottom': 0, 'border-bottom': 'none'});
		if (this.options.width) element.setStyles({'padding-left': 0, 'border-left': 'none', 'padding-right': 0, 'border-right': 'none'});
		element.fullOpacity = 1;
		if (this.options.fixedWidth) element.fullWidth = this.options.fixedWidth;
		if (this.options.fixedHeight) element.fullHeight = this.options.fixedHeight;
		element.setStyle('overflow', 'hidden');
		if (!test){
			for (var fx in this.effects) element.setStyle(fx, 0);
		}
		return this;
	},

	display: function(index){
		index = ($type(index) == 'element') ? this.elements.indexOf(index) : index;
		if ((this.timer && this.options.wait) || (index === this.previous && !this.options.alwaysHide)) return this;
		this.previous = index;
		var obj = {};
		this.elements.each(function(el, i){
			obj[i] = {};
			var hide = (i != index) || (this.options.alwaysHide && (el.offsetHeight > 0));
			this.fireEvent(hide ? 'onBackground' : 'onActive', [this.togglers[i], el]);
			for (var fx in this.effects) obj[i][fx] = hide ? 0 : el[this.effects[fx]];
		}, this);
		return this.start(obj);
	},

	showThisHideOpen: function(index){return this.display(index);}

});

Fx.Accordiono = Accordiono;

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
		if (obj.offsetParent) {
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
		if (obj.offsetParent) {
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
		HUB.Base.menu();
		HUB.Base.setLoginFocus();
		HUB.Base.popups();
		
		// Init SqueezeBox
		SqueezeBoxHub.initialize({ size: {x: 760, y: 520} });
		
		HUB.Base.overlayer();

		// Init Growl
		Growl.Bezel = new Gr0wl.Bezel(HUB.Base.templatepath+'images/bezel.png');
		Growl.Smoke = new Gr0wl.Smoke(HUB.Base.templatepath+'images/smoke.png');
		
		// Init the accordian
		var accordion = new Accordiono($$('.toggler'),$$('.element'), {
			opacity: 0, 
			onBackground: function(toggler) { toggler.setStyle('color', '#000000'); }  
		});
		accordion.showThisHideOpen(2);
		
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
