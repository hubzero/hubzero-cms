/**
 * @package     hubzero-cms
 * @file        plugins/system/debug/assets/js/debug.js
 * @copyright   Copyright (c) 2005-2020 The Regents of the University of California.
 * @license     http://opensource.org/licenses/MIT MIT
 */

if (!document.getElementsByClassName) {
	document.getElementsByClassName = (function() {
		function traverse (node, callback) {
			callback(node);
			for (var i=0;i < node.childNodes.length; i++) {
				traverse(node.childNodes[i],callback);
			}
		}
		return function (name) {
			var result = [];
			traverse(document.body,function(node) {
				if (node.className && (' ' + node.className + ' ').indexOf(' ' + name + ' ') > -1) {
					result.push(node);
				}
			});
			return result;
		}
	})();
}

Debugger = {
	toggleShortFull: function(id) {
		var d = document.getElementById('debug-' + id + '-short');
		if (!Debugger.hasClass(d, 'open')) {
			Debugger.addClass(d, 'open');
		} else {
			Debugger.removeClass(d, 'open');
		}

		var g = document.getElementById('debug-' + id + '-full');
		if (!Debugger.hasClass(g, 'open')) {
			Debugger.addClass(g, 'open');
		} else {
			Debugger.removeClass(g, 'open');
		}
	},
	close: function() {
		var d = document.getElementById('system-debug');
		if (Debugger.hasClass(d, 'open')) {
			Debugger.removeClass(d, 'open');
		}

		Debugger.deactivate();
	},
	deactivate: function() {
		var items = document.getElementsByClassName('debug-tab');
		for (var i=0;i<items.length;i++)
		{
			if (Debugger.hasClass(items[i], 'active')) {
				Debugger.removeClass(items[i], 'active');
			}
		}

		var items = document.getElementsByClassName('debug-container');
		for (var i=0;i<items.length;i++)
		{
			if (Debugger.hasClass(items[i], 'open')) {
				Debugger.removeClass(items[i], 'open');
			}
		}
	},
	toggleContainer: function(el, name) {
		if (!Debugger.hasClass(el, 'active')) {
			var d = document.getElementById('system-debug');
			if (!Debugger.hasClass(d, 'open')) {
				Debugger.addClass(d, 'open');
			}

			Debugger.deactivate();
			Debugger.addClass(el, 'active');

			var e = document.getElementById(name);
			if (e) {
				Debugger.toggleClass(e, 'open');
			}
		} else {
			Debugger.close();
		}
	},
	hasClass: function(elem, className) {
		return new RegExp(' ' + className + ' ').test(' ' + elem.className + ' ');
	},
	addClass: function(elem, className) {
		if (!Debugger.hasClass(elem, className)) {
			elem.className += ' ' + className;
		}
	},
	removeClass: function(elem, className) {
		var newClass = ' ' + elem.className.replace(/[\t\r\n]/g, ' ') + ' ';
		if (Debugger.hasClass(elem, className)) {
			while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
				newClass = newClass.replace(' ' + className + ' ', ' ');
			}
			elem.className = newClass.replace(/^\s+|\s+\$/g, '');
		}
	},
	toggleClass: function(elem, className) {
		var newClass = ' ' + elem.className.replace( /[\t\r\n]/g, ' ') + ' ';
		if (Debugger.hasClass(elem, className)) {
			while (newClass.indexOf(' ' + className + ' ') >= 0 ) {
				newClass = newClass.replace(' ' + className + ' ', ' ');
			}
			elem.className = newClass.replace(/^\s+|\s+\$/g, '');
		} else {
			elem.className += ' ' + className;
		}
	},
	addEvent: function(obj, type, fn) {
		if (obj.attachEvent) {
			obj['e'+type+fn] = fn;
			obj[type+fn] = function() {
				obj['e'+type+fn]( window.event );
			};
			obj.attachEvent('on' + type, obj[type+fn]);
		} else {
			obj.addEventListener( type, fn, false );
		}
	},
	removeEvent: function( obj, type, fn ) {
		if (obj.detachEvent) {
			obj.detachEvent('on' + type, obj[type+fn]);
			obj[type+fn] = null;
		} else {
			obj.removeEventListener(type, fn, false);
		}
	}
};

Function.prototype.bindD = function(obj) {
	var _method = this;
	return function() {
		return _method.apply(obj, arguments);
	};
}

function debugDrag(id) {
	this.id = 'id';
	this.direction = 'y';
}

debugDrag.prototype = {
	init: function(settings) {
		for (var i in settings)
		{
			this[i] = settings[i];

			for (var j in settings[i])
			{
				this[i][j] = settings[i][j];
			}
		}

		this.elem = (this.id.tagName==undefined) ? document.getElementById(this.id) : this.id;
		this.container = this.elem.parentNode;
		this.elem.onmousedown = this._mouseDown.bindD(this);
	},

	_mouseDown: function(e) {
		e = e || window.event;

		this.elem.onselectstart=function() {return false};

		this._event_docMouseMove = this._docMouseMove.bindD(this);
		this._event_docMouseUp = this._docMouseUp.bindD(this);

		if (this.onstart) this.onstart();

		this.x = e.clientX || e.PageX;
		this.y = e.clientY || e.PageY;

		//this.left = parseInt(this._getstyle(this.elem, 'left'));
		//this.top = parseInt(this._getstyle(this.elem, 'top'));
		this.top = parseInt(this._getstyle(this.container, 'height'));

		Debugger.addEvent(document, 'mousemove', this._event_docMouseMove);
		Debugger.addEvent(document, 'mouseup', this._event_docMouseUp);

		return false;
	},

	_getstyle: function(elem, prop) {
		if (document.defaultView) {
			return document.defaultView.getComputedStyle(elem, null).getPropertyValue(prop);
		} else if (elem.currentStyle) {
			var prop = prop.replace(/-(\w)/gi, function($0,$1)
			{
				return $1.toUpperCase();
			});
			return elem.currentStyle[prop];
		} else {
			return null;
		}
	},

	_docMouseMove: function(e) {
		this.setValuesClick(e);
		if (this.ondrag) {
			this.ondrag();
		}
	},

	_docMouseUp: function(e) {
		Debugger.removeEvent(document, 'mousemove', this._event_docMouseMove);

		if (this.onstop) {
			this.onstop();
		}

		Debugger.removeEvent(document, 'mouseup', this._event_docMouseUp);
	},

	setValuesClick: function(e) {
		if (!Debugger.hasClass(this.container, 'open')) {
			return;
		}

		this.mouseX = e.clientX || e.PageX;
		this.mouseY = e.clientY || e.pageY;

		this.Y = this.top + this.y - this.mouseY - parseInt(this._getstyle(document.getElementById('debug-head'), 'height')); //this.top + this.mouseY - this.y;

		//this.container.style.height = (this.Y + 6) +'px';
		document.getElementById('debug-body').style.height = (this.Y + 6) +'px';
	},

	_limit: function(val, mn, mx) {
		return Math.min(Math.max(val, Math.min(mn, mx)), Math.max(mn, mx));
	}
}

document.addEventListener('DOMContentLoaded', function() {
	var dragBar = new debugDrag();
	dragBar.init({id:'debug-head'});

	var btns = document.getElementsByClassName('debug-close-btn');
	for (var i=0; i<btns.length; i++)
	{
		btns[i].addEventListener('click', function(e){
			e.preventDefault();

			Debugger.close();
		});
	}

	var tabs = document.getElementsByClassName('debug-tab');
	for (var i=0; i<tabs.length; i++)
	{
		tabs[i].addEventListener('click', function(e){
			e.preventDefault();

			Debugger.toggleContainer(this, this.href.substring(this.href.indexOf('#')+1));
		});
	}

	var scts = document.getElementsByClassName('debug-toggle');
	for (var i=0; i<scts.length; i++)
	{
		scts[i].addEventListener('click', function(e){
			e.preventDefault();

			Debugger.toggleShortFull(this.getAttribute('data-section'));
		});
	}
});
