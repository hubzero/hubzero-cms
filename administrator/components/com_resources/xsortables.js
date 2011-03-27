/**
 * @package     hubzero-cms
 * @file        administrator/components/com_resources/xsortables.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

var xSortables = new Class({

	options: {
		constrain : false,
		clone: true,
		opacity: 0.7,
		handle: false,
		revert: false,
		onStart: Class.empty,
		onComplete: Class.empty
	},
	
	initialize: function(lists, options){
		this.setOptions(options);
		this.idle = true;
		this.hovering = false;
		this.newInsert = false;
		this.bound = {
			start: [],
			end: this.end.bindWithEvent(this),
			move: this.move.bindWithEvent(this),
			reset: this.reset.bindWithEvent(this)
		};
		if (this.options.revert){
			var revertOptions = $merge({duration: 250, wait: false}, this.options.revert);
			this.effect = new Fx.Styles(this.element, revertOptions).addEvent('onComplete', this.bound.reset, true);
		}
		this.cloneContents = !!(this.options.clone);

		this.lists = $$($(lists) || lists);
		
		this.reinitialize();
		if (this.options.initialize) this.options.initialize.call(this);
	},

	reinitialize: function(){
		if (this.handles) this.detach();
		
		this.handles = [];
		var elements = [];
		
		this.lists.each(function(list){
			elements.extend(list.getChildren());
		});

		this.handles = !this.options.handle ? elements : elements.map(function(element){
			return element.getElement(this.options.handle) || element;
		}.bind(this));
		
		this.handles.each(function(handle, i){
			this.bound.start[i] = this.start.bindWithEvent(this, elements[i]);
		}, this);

		this.attach();
	},

	attach: function(){
		this.handles.each(function(handle, i){
			handle.addEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	detach: function(){
		this.handles.each(function(handle, i){
			handle.removeEvent('mousedown', this.bound.start[i]);
		}, this);
	},

	check: function(element, list){
		element = element.getCoordinates();
		var coords = list ? element : {
			left: element.left - this.list.scrollLeft,
			right: element.right - this.list.scrollLeft,
			top: element.top - this.list.scrollTop,
			bottom: element.bottom - this.list.scrollTop
		};
		return (this.curr.x > coords.left && this.curr.x < coords.right && this.curr.y > coords.top && this.curr.y < coords.bottom);
	},

	where: function(element){
		if (this.newInsert){
			this.newInsert = false;
			return 'before';
		}
		var dif = {'x': this.curr.x - this.prev.x, 'y': this.curr.y - this.prev.y};
		return dif[['y', 'x'][(Math.abs(dif.x) >= Math.abs(dif.y)) + 0]] <= 0 ? 'before' : 'after';
	},

	reposition: function(){
		if (this.list.positioned){
			this.position.y -= this.offset.list.y - this.list.scrollTop;
			this.position.x -= this.offset.list.x - this.list.scrollLeft;
		//} else if (Client.Engine.opera){
			//this.position.y += this.list.scrollTop;
			//this.position.x += this.list.scrollLeft;
		}
	},

	start: function(event, element){
		if (!this.idle) return;

		this.idle = false;
		this.prev = {'x': event.page.x, 'y': event.page.y};

		this.styles = element.getStyles('margin-top', 'margin-left', 'padding-top', 'padding-left', 'border-top-width', 'border-left-width', 'opacity');
		this.margin = {
			'top': this.styles['margin-top'].toInt() + this.styles['border-top-width'].toInt(),
			'left': this.styles['margin-left'].toInt() + this.styles['border-left-width'].toInt()
		};

		this.element = element;
		this.list = this.element.getParent();
		this.list.hovering = this.hovering = true;
		this.list.positioned = this.list.getStyle('position').test(/relative|absolute|fixed/);

		var children = this.list.getChildren();
		var bounds = children.shift().getCoordinates();
		children.each(function(element){
			var coords = element.getCoordinates();
			bounds.left = Math.min(coords.left, bounds.left);
			bounds.right = Math.max(coords.right, bounds.right);
			bounds.top = Math.min(coords.top, bounds.top);
			bounds.bottom = Math.max(coords.bottom, bounds.bottom);
		});
		this.bounds = bounds;

		this.position = this.element.getPosition([this.list]);

		this.offset = {
			'list': this.list.getPosition(),
			'element': {'x': event.page.x - this.position.x, 'y': event.page.y - this.position.y}
		};
		this.reposition();

		var clone = this.options.clone;
		switch ($type(clone)){
			case 'function': this.clone = clone.call(this, this.element); break;
			case 'boolean': clone = (clone) ? {'opacity': 0.7} : {'visibility': 'hidden'};
			case 'object': this.clone = this.element.clone(this.cloneContents).setStyles(clone);
		}

		this.clone.injectBefore(this.element.setStyles({
			'position': 'absolute',
			'top': this.position.y - this.margin.top,
			'left': this.position.x - this.margin.left,
			'opacity': this.options.opacity,
			'width': this.element.getSize().size.x
		}));

		document.addEvent('mousemove', this.bound.move);
		document.addEvent('mouseup', this.bound.end);
		this.fireEvent('onStart', this.element);
		event.stop();
	},

	move: function(event){
		this.curr = {'x': event.page.x, 'y': event.page.y};
		this.position = {'x': this.curr.x - this.offset.element.x, 'y': this.curr.y - this.offset.element.y};

		if (this.options.constrain) {
			this.position.y = this.position.y.limit(this.bounds.top, this.bounds.bottom - this.element.offsetHeight);
			this.position.x = this.position.x.limit(this.bounds.left, this.bounds.right - this.element.offsetWidth);
		}
		this.reposition();
		this.element.setStyles({
			'top' : this.position.y - this.margin.top,
			'left' : this.position.x - this.margin.left
		});

		if (!this.options.constrain){
			var oldSize, newSize;
			this.lists.each(function(list){
				if (!this.check(list, true)){
					list.hovering = false;
				} else if (!list.hovering){
					this.list = list;
					this.list.hovering = this.newInsert = true;
					this.list.positioned = this.list.getStyle('position').test(/relative|absolute|fixed/);
					oldSize = this.clone.getSize().size;
					this.list.adopt(this.clone, this.element);
					newSize = this.clone.getSize().size;
					this.offset = {
						'list': this.list.getPosition(),
						'element': {
							'x': Math.round(newSize.x * (this.offset.element.x / oldSize.x)),
							'y': Math.round(newSize.y * (this.offset.element.y / oldSize.y))
						}
					};
				}
			}, this);
		}

		if (this.list.hovering){
			this.list.getChildren().each(function(element){
				if (!this.check(element)){
					element.hovering = false;
				} else if (!element.hovering && element != this.clone){
					element.hovering = true;
					this.clone.inject(element, this.where(element));
				}
			}, this);
		}

		this.prev = this.curr;
		event.stop();
	},

	end: function(){
		this.prev = null;
		document.removeEvent('mousemove', this.bound.move);
		document.removeEvent('mouseup', this.bound.end);

		this.position = this.clone.getPosition([this.list]);
		this.reposition();

		if (!this.effect){
			this.reset();
		} else {
			this.effect.element = this.element;
			this.effect.start({
				'top' : this.position.y - this.margin.top,
				'left' : this.position.x - this.margin.left,
				'opacity' : this.styles.opacity
			});
		}
	},

	reset: function(){
		this.element.setStyles({
			'position': 'static',
			'opacity': this.styles.opacity
		}).injectBefore(this.clone);
		this.clone.empty().remove();

		this.fireEvent('onComplete', this.element);
		this.idle = true;
	},

	serialize: function(index, modifier){
		var map = modifier || function(element, index){
			return element.getProperty('id');
		}.bind(this);
		
		var serial = this.lists.map(function(list){
			return list.getChildren().map(map, this);
		}, this);

		if (this.lists.length == 1) index = 0;
		return $chk(index) && index >= 0 && index < this.lists.length ? serial[index] : serial;
	}
});

xSortables.implement(new Events, new Options);

