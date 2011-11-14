/**
 * @package     hubzero-cms
 * @file        plugins/hubzero/autocompleter/textboxlist.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

/*
  Moogets - TextboxList 0.2
  - MooTools version required: 1.2
  - MooTools components required: Element.Event, Element.Style and dependencies.
  
  Credits:
  - Idea: Facebook + Apple Mail
  - Caret position method: Diego Perini <http://javascript.nwbox.com/cursor_position/cursor.js>
  
  Changelog:
  - 0.1: initial release
  - 0.2: code cleanup, small blur/focus fixes
*/
var get = function(uid){
	return (storage[uid] || (storage[uid] = {}));
};

storage = {};

Element.extend({
	retrieve: function(property, dflt){
		var storage = get(this.uid), prop = storage[property];
		if (dflt != undefined && prop == undefined) prop = storage[property] = dflt;
		return $pick(prop);
	},
	
	store: function(property, value){
		var storage = get(this.uid);
		storage[property] = value;
		return this;
	},
	
	destroy: function(){
		Element.empty(this);
		Element.dispose(this);
		//clean(this, true);
		return null;
	},
	
	empty: function(){
		$A(this.childNodes).each(function(node){
			Element.destroy(node);
		});
		return this;
	},

	dispose: function(){
		return (this.parentNode) ? this.parentNode.removeChild(this) : this;
	}
});

Event.Keys = new Abstract({
	'enter': 13,
	'up': 38,
	'down': 40,
	'left': 37,
	'right': 39,
	'esc': 27,
	'space': 32,
	'backspace': 8,
	'tab': 9,
	'delete': 46,
	'comma': 44
});

/* Copyright: Guillermo Rauch <http://devthought.com/> - Distributed under MIT - Keep this message! */
Element.extend({
	getCaretPosition: function() {
		if (this.createTextRange) {
			var r = document.selection.createRange().duplicate();
			r.moveEnd('character', this.value.length);
			if (r.text === '') return this.value.length;
			return this.value.lastIndexOf(r.text);
		} else return this.selectionStart;
	}
});

var ResizableTextbox = new Class({
	
	options: {
		len: 100,
		min: 150,
		max: 500,
		step: 7
	},
	
	initialize: function(element, options) {
		var that = this;
		this.setOptions(options);
		this.el = $(element);
		this.width = this.el.offsetWidth;
		this.el.addEvents({
			'keydown': function() {
				this.store('rt-value', this.getProperty('value').length);
			},
			'keyup': function() {
				if (this.getProperty('value').length > that.options.len) { 
					this.value = this.getProperty('value').substr(0, that.options.len);
					alert("Text too long. Must be 100 characters or less"); 
				}
				var newsize = that.options.step * this.getProperty('value').length;
				if(newsize <= that.options.min) newsize = that.width;
				if(! (this.getProperty('value').length == this.retrieve('rt-value') || newsize <= that.options.min || newsize >= that.options.max))
					this.setStyle('width', newsize);
			}
		});
	}
});
ResizableTextbox.implement(new Options);

var TextboxList = new Class({

	options: {/*
		onFocus: $empty,
		onBlur: $empty,
		onInputFocus: $empty,
		onInputBlur: $empty,
		onBoxFocus: $empty,
		onBoxBlur: $empty,
		onBoxDispose: $empty,*/
		resizable: {},
		className: 'bit',
		separator: ', ',
		extrainputs: true,
		startinput: true,
		hideempty: true,
		listcls: 'act',
		inputid: ''
	},
  
	initialize: function(element, options) {
		this.setOptions(options);
		this.element = $(element).setStyle('display', 'none');    
		this.bits = new Hash;
		this.events = new Hash;
		this.count = 0;
		this.current = false;
		this.maininput = this.createInput({'class': 'maininput','id':'maininput-'+this.options.inputid});
		this.holder = new Element('ul', {
			'class': 'textboxlist-holder '+this.options.listcls, 
			'events': {
				'click': function(e) { 
					e = new Event(e).stop();
					if(this.maininput != this.current) this.focus(this.maininput);
				}.bind(this)
			}
		}).inject(this.element, 'before').adopt(this.maininput);
		this.makeResizable(this.maininput);
		this.setValues();
		this.setEvents();
	},
  
	setValues: function() {
		value = this.element.getProperty('value');
		if (value) {
			values = value.split(',');
			values.each(function(v){
				if (v) this.add.apply(this, $type(v) == 'array' ? [v[1], v[0], v[2]] : [v]);
			}, this);
		}
	},
	
	setEvents: function() {
		//document.addEvent(Browser.Engine.trident ? 'keydown' : 'keypress', function(e) {    
		document.addEvent('keypress', function(e) {
			if(! this.current) return;
			if(this.current.retrieve('type') == 'box' && e.code == Event.Keys.backspace) new Event(e).stop();
		}.bind(this));
		
		document.addEvents({
			'keyup': function(e) { 
				e = new Event(e).stop();
				if(! this.current) return;
				switch(e.code){
					case Event.Keys.left: return this.move('left');
					case Event.Keys.right: return this.move('right');
					case Event.Keys.backspace: return this.moveDispose();
				}
			}.bind(this),
			'click': function() { this.fireEvent('onBlur').blur(); }.bind(this)
		});

		/*$$('input.maininput').each(function(el) {
		el.addEvents({
			'keyup': function(e) { 
				e = new Event(e);
				if(! this.current) return;
				if (e.code === Event.Keys.enter) {
					e.stop();
					return this.add(el.getProperty('value'));
				}
			}.bind(this)
		});
		});*/
	},
	
	update: function() {
		this.element.setProperty('value', this.bits.values().join(this.options.separator));
		return this;
	},
	
	add: function(text, html, rid) {
		var id = (rid) ? 'ac'+rid : this.options.className + '-' + this.count++;
		var el = this.createBox($pick(html, text), {'id': id}).inject(this.current || this.maininput, 'before');
		el.addEvent('click', function(e) {
			e = new Event(e).stop();
			this.focus(el);
		}.bind(this));
		text = (rid) ? rid : text;
		this.bits.set(id, text);
		if(this.options.extrainputs && (this.options.startinput || el.getPrevious())) this.addSmallInput(el, 'before');
		this.update();
		return el;
	},
	
	addSmallInput: function(el, where) {
		var input = this.createInput({'class': 'smallinput'}).inject(el, where);
		input.store('small', true);
		this.makeResizable(input);
		if(this.options.hideempty) input.setStyle('display', 'none');
		return input;
	},
	
	dispose: function(el) {
		this.bits.remove(el.id);
		if(el.getPrevious().retrieve('small')) el.getPrevious().destroy();
		if(this.current == el) this.focus(el.getNext());
		if(el.retrieve('type') == 'box') this.fireEvent('onBoxDispose', el);
		el.destroy();
		this.update();
		return this;
	},
	
	focus: function(el, nofocus) {
		if(! this.current) this.fireEvent('onFocus', el);
		else if(this.current == el) return this;
		this.blur();
		el.addClass(this.options.className + '-' + el.retrieve('type') + '-focus');
		if(el.retrieve('small')) el.setStyle('display', 'block');
		if(el.retrieve('type') == 'input') {
			this.fireEvent('onInputFocus', el);
			//if(! nofocus) this.callEvent(el.retrieve('input'), 'focus');
			$('maininput-'+this.options.inputid).focus();
			//if(! nofocus) this.callEvent(el, 'focus');
		}
		else this.fireEvent('onBoxFocus', el);
		this.current = el;
		return this;
	},
	
	blur: function(noblur) {
		if(! this.current) return this;
		if(this.current.retrieve('type') == 'input') {
			var input = this.current.retrieve('input');
			if(! noblur) this.callEvent(input, 'blur');
			this.fireEvent('onInputBlur', input);
		}
		else this.fireEvent('onBoxBlur', this.current);
		if(this.current.retrieve('small') && ! input.getProperty('value') && this.options.hideempty) 
			this.current.setStyle('display', 'none');
		this.current.removeClass(this.options.className + '-' + this.current.retrieve('type') + '-focus');
		this.current = false;
		return this;
	},
	
	createBox: function(text, options) {
		return new Element('li', $extend(options, {'class': this.options.className + '-box'})).setHTML(text).store('type', 'box');
	},

	createInput: function(options) {
		var li = new Element('li', {'class': this.options.className + '-input'});
		var el = new Element('input', $extend(options, {
			'type': 'text', 
			'events': {
				'click': function(e) { e = new Event(e).stop(); },
				'focus': function(e) { if(! this.isSelfEvent('focus')) this.focus(li, true); }.bind(this),
				'blur': function() { 
					//if(! this.isSelfEvent('blur')) this.blur(true); 
					v = el.getProperty('value').clean().replace(/,/g, '');
					//if (v != '' && this.element.getProperty('value') == '') {
					if (v != '') {
						el.setProperty('value','');
						return this.add(v);
					}
				}.bind(this),
				'keydown': function(e) { this.store('lastvalue', this.value).store('lastcaret', this.getCaretPosition()); },
				'keypress': function(e) { // keyup failed in Safari (?!)
					e = new Event(e);
					if(! this.current) return;
					if (e.code === Event.Keys.enter || e.code === Event.Keys.comma) {
						e.stop();
						v = el.getProperty('value').clean().replace(/,/g, '');
						el.setProperty('value','');
						return this.add(v);
					}
				}.bind(this)
			}
		}));
		return li.store('type', 'input').store('input', el).adopt(el);
	},

	callEvent: function(el, type) {
		this.events.set(type, el);
		el[type]();
	},
  
	isSelfEvent: function(type) {
		return (this.events.get(type)) ? !! this.events.remove(type) : false;
	},
	
	makeResizable: function(li) {
		var el = li.retrieve('input');
		var minw = (el.offsetWidth > 150) ? el.offsetWidth : 150; //min: minw, max: this.element.getStyle('width').toInt()
		el.store('resizable', new ResizableTextbox(el, $extend(this.options.resizable, {})));
		return this;
	},
  
	checkInput: function() {
		var input = this.current.retrieve('input');
		return (! input.retrieve('lastvalue') || (input.getCaretPosition() === 0 && input.retrieve('lastcaret') === 0));
	},
  
	move: function(direction) {
		var el = this.current['get' + (direction == 'left' ? 'Previous' : 'Next')]();
		if(el && (! this.current.retrieve('input') || ((this.checkInput() || direction == 'right')))) this.focus(el);
		return this;
	},
	
	moveDispose: function() {
		if(this.current.retrieve('type') == 'box') return this.dispose(this.current);
		if(this.checkInput() && this.bits.keys().length && this.current.getPrevious()) {
			//return this.focus(this.current.getPrevious());
			return this.dispose(this.current.getPrevious());
		}
	}
});
TextboxList.implement(new Events, new Options);

// Extends: TextboxList
AppleboxList = TextboxList.extend({  
	createBox: function(text, options) {
		var li = new Element('li', $extend(options, {'class': this.options.className + '-box'})).setHTML(text).store('type', 'box');
		li.addEvents({
			'mouseenter': function() { this.addClass('bit-hover') },
			'mouseleave': function() { this.removeClass('bit-hover') }
		});
		li.adopt(new Element('a', {
			'href': '#',
			'class': 'closebutton',
			'events': {
				'click': function(e) {
					new Event(e).stop();
					if(! this.current) this.focus(this.maininput);
					this.dispose(li);
				}.bind(this)
			}
		}));
		return li;
	}
});
