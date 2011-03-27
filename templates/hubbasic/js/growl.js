/**
 * @package     hubzero-cms
 * @file        templates/hubbasic/js/growl.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
//  Window.Growl, version 2.0: http://icebeat.bitacoras.com
//  Daniel Mota aka IceBeat <daniel.mota@gmail.com>
//-----------------------------------------------------------
var Gr0wl = {};

Gr0wl.Base = new Class({
	
	options: {
		image: 'growl.jpg',
		title: 'Window.Growl by Daniel Mota',
		text: 'http://icebeat.bitacoras.com',
		duration: 2
	},
	
	initialize: function(image) {
		this.image = new Asset.image(image, { onload: this.create.bind(this) });
		return this.show.bind(this);
	},
	
	create: function(styles) {
		this.image.setStyles('position:absolute;display:none').setOpacity(0).injectInside(document.body);
		this.block = new Element('div').setStyles('position:absolute;display:none;z-index:999;color:#fff;font: 12px/14px "Lucida Grande", Arial, Helvetica, Verdana, sans-serif;'+styles.div).setOpacity(0).injectInside(document.body);
		new Element('img').setStyles(styles.img).injectInside(this.block);
		new Element('h3').setStyles(styles.h3).injectInside(this.block);
		new Element('p').setStyles(styles.p).injectInside(this.block);
	},
	
	show: function(options) {
		options = $merge(this.options, options);
		var elements = [this.image.clone(), this.block.clone()];
		elements.each(function(e, i) {
			e.injectInside(document.body);
			e.setStyles(options.position);
			if(i) e.getFirst().setProperty('src', options.image).getNext().setHTML(options.title).getNext().setHTML(options.text);
		});
		new Fx.Elements(elements, {duration:400}).start({
			'0': { 'opacity': 0.75 }, '1': { 'opacity': 1 }
		});
		this.hide.delay(options.duration*1000, this, [elements]);
	},
	
	hide: function(elements, effect) {
		var effects = new Fx.Elements(elements, {duration:400, onComplete: function() {
			this.elements[0].remove();
			this.elements[1].empty().remove();
		}}).start({'0': effect, '1': effect });
	}
	
});

Gr0wl.Smoke = Gr0wl.Base.extend({
	
	create: function() {
		this.queue = [];
		this.parent({
			div: 'width:298px;height:73px;',
			img: 'float:left;margin:12px;',
			h3: 'margin:0;padding:10px 0px;font-size:13px;',
			p: 'margin:0px 10px;font-size:12px;'
		});
	},
	
	show: function(options) {
		var last = this.queue.getLast(),
		delta = window.getScrollTop()+10+(last*83);
		options.position = {'top':delta+'px', 'right':'10px', 'display':'block'};
		this.queue.push(last+1);
		this.parent(options);
	},
	
	hide: function(elements) {
		this.queue.shift();
		this.parent(elements,{ 'opacity': 0 });
	}
	
});

Gr0wl.Bezel = Gr0wl.Base.extend({
	
	create: function() {
		this.i=0;
		this.parent({
			div: 'width:211px;height:206px;text-align:center;',
			img: 'margin-top:25px;',
			h3: 'margin:0;padding:0px;padding-top:22px;font-size:14px;color:#fff;',
			p: 'margin:15px;font-size:12px;'
		});
	},
	
	show: function(options) {
		var top = window.getScrollTop()+(window.getHeight()/2)-105,
		left = window.getScrollLeft()+(window.getWidth()/2)-103;
		options.position = {'top':top+'px', 'left':left+'px', 'display':'block'};
		this.i++;
		this.chain(this.parent.pass(options,this));
		if(this.i==1) this.callChain();
	},
	
	hide: function(elements) {
		this.queue.delay(400,this);
		this.parent(elements, { 'opacity': 0, 'margin-top': [0,50] });
	},
	
	queue: function() {
		this.i--;
		this.callChain();
	}
	
});

Gr0wl.Bezel.implement(new Chain);

var Growl = function(options) {
	if(Growl[options.type]) Growl[options.type].call(options);
	else Growl.Smoke(options);
};

