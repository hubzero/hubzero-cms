/**
* @version		$Id: tips.js 491 2010-01-23 12:09:30Z happynoodleboy $
* @package      JCE
* @copyright    Copyright (C) 2005 - 2010 Ryan Demmer. All rights reserved.
* @copyright	copyright (c) 2007 Valerio Proietti, <http://mad4milk.net>
* @author		Ryan Demmer
* @license      MIT
* JCE is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
*/

/**
 * Based on the Mootools Tips class with additional options
 * Tooltip div is created when the first tooltip is initialized, not on page load
 * Changes to locate function allow tooltip to be positioned relative to the mouse pointer
 * @param {Object} tip
 */
var JCETips = new Class({

	getOptions: function(){
		return {
			onShow: function(tip){
				tip.setStyle('visibility', 'visible');
			},
			onHide: function(tip){
				tip.setStyle('visibility', 'hidden');
			},
			speed: 150,
			position: 'bottom right',
			opacity: 0.9,
			className: 'tooltip',
			offsets: {
				'x': 16,
				'y': 16
			},
			width : 200,
			fixed: false
		}
	},
	/**
	 * Initilize the class
	 * @param {Array} elements Array of elements
	 * @param {Object} options Options object
	 */
	initialize: function(elements, options){
		this.setOptions(this.getOptions(), options);
		$$(elements).each(function(el){
			el.addEvents({
				'mouseenter': 	this.start.bindWithEvent(this, el),
				'mousemove':	this.move.bindWithEvent(this, el), 
				'mouseleave': 	this.end.bindWithEvent(this, el)
			});
		}.bind(this));
	},
	add : function(elements) {
		$$(elements).each(function(el){
			el.addEvents({
				'mouseenter': 	this.start.bindWithEvent(this, el),
				'mousemove':	this.move.bindWithEvent(this, el), 
				'mouseleave': 	this.end.bindWithEvent(this, el)
			});
		}.bind(this));
	},
	
	remove : function(elements) {
		$$(elements).each(function(el){
			el.removeEvents('mouseenter');
			el.removeEvents('mousemove');
			el.removeEvents('mouseleave');
		});
	},
	
	/**
	 * Create the tooltip div
	 */
	create : function() {
		if (!this.toolTip) {		
			this.toolTip = new Element('div', {
				'class': this.options.className,
				'styles': {
					'position'	: 'absolute',
					'top'		: '0',
					'left'		: '0',
					'visibility': 'hidden',
					'opacity' 	: this.options.opacity,
					'width'		: this.options.width	
				}
			}).injectInside($E('body'));

			this.wrapper = new Element('div').addClass(this.options.className + '_wrapper').inject(this.toolTip);
		}
	},
	/**
	 * Show the tooltip and build the tooltip text
	 * @param {Object} e  Event
	 * @param {Object} el Target Element
	 */
	start: function(e, el){				
		this.create();
		
		var text = el.title || '', title = '';
			
		if(/::/.test(text)){
			var parts 	= text.split('::');
			title 		= parts[0].trim();
			text 		= parts[1].trim();
		}
		// Inherit parent classes
		var cls 		= el.className.replace(/(jce_?)tooltip/gi, '');		
		// Store original title and remove
		this.toolTip.title 	= el.title;			
		$(el).setProperty('title', '');
		
		this.wrapper.empty();

		if (title){
			this.title = new Element('h4').inject(this.wrapper).setHTML(title);
		}
		if (text){
			this.text = new Element('p').inject(this.wrapper).setHTML(text);
		}

		$clear(this.timer);
		this.timer = this.show.delay(this.options.showDelay, this);
	},
	
	move : function(e, el) {
		if(this.options.fixed) {
			this.position(el);
		} else {
			this.locate(e);
		}
	},

	end: function(event, el){
		$clear(this.timer);
		el.setProperty('title', this.toolTip.title);
		this.timer = this.hide.delay(this.options.hideDelay, this);
	},
	
	position: function(element){
		this.create();
		var pos = element.getPosition();
		this.toolTip.setStyles({
			'left': pos.x + this.options.offsets.x,
			'top': pos.y + this.options.offsets.y
		});
	},

	/**
	 * Position the tooltip
	 * @param {Object} e Event trigger
	 */
	locate : function(e){				
		this.create();
		
		var o 		= this.options.offsets;
		var page 	= e.page;
		var tip 	= {'x': this.toolTip.offsetWidth, 'y': this.toolTip.offsetHeight};
		var pos 	= {'x': page.x + o.x, 'y': page.y + o.y};
		
		var ah 		= 0;
		
		var position = this.options.position;
		
		// Switch from right to left
		if ((tip.x + pos.x) > window.getWidth()) { 
			this.toolTip.removeClass(this.options.className + '_right');
			position = position.replace('right', 'left');
			this.toolTip.addClass(this.options.className + '_left');
		} else {
			this.toolTip.removeClass(this.options.className + '_left');
			position = position.replace('left', 'right');
			this.toolTip.addClass(this.options.className + '_right');
		}
		
		// Switch from bottom to top
		if ((tip.y + pos.y) > window.getHeight()) {
			this.toolTip.removeClass(this.options.className + '_bottom');
			position = position.replace('bottom', 'top');
			this.toolTip.addClass(this.options.className + '_top');
		} else {
			this.toolTip.removeClass(this.options.className + '_top');
			position = position.replace('top', 'bottom');
			this.toolTip.addClass(this.options.className + '_bottom');
		}
				
		switch(position){
			case 'top left':
				pos.x = (page.x - tip.x) - o.x;
				pos.y = (page.y - tip.y) - (ah + o.y);
				break;
			case 'top right':
				pos.x = page.x + o.x;
				pos.y = (page.y - tip.y) - (ah + o.y);
				break;
			case 'top center':
				pos.x = (page.x - Math.round((tip.x / 2))) + o.x;
				pos.y = (page.y - tip.y) - (ah + o.y);
				break;
			case 'bottom left':
				pos.x = (page.x - tip.x) - o.x;
				pos.y = (page.y + Math.round((tip.y/2))) - (ah + o.y);
				break;
			case 'bottom right':
				pos.x = page.x + o.x;
				pos.y = page.y + o.y;
				break;
			case 'bottom center':
				pos.x = (page.x - (tip.x/2)) + o.x;
				pos.y = page.y + ah + o.y;
				break;
		}
		$(this.toolTip).setStyles({
			top: pos.y + 'px', 
			left: pos.x + 'px'
		});
	},

	/**
	 * Execute the onShow function
	 */
	show: function(){
		if (this.options.timeout) this.timer = this.hide.delay(this.options.timeout, this);
		this.fireEvent('onShow', [this.toolTip]);
	},
	/**
	 * Execute the onHide function
	 */
	hide: function(){
		this.fireEvent('onHide', [this.toolTip]);
	}

});

JCETips.implement(new Events, new Options);
