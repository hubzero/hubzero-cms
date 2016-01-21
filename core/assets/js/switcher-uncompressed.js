/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Switcher behavior
 *
 * @package		Joomla
 * @since		1.5
 */
;(function(jQuery, window, document, undefined) {

	var pluginName = 'switcher',
		defaults = {
			onShow: function(){},
			onHide: function(){},
			cookieName: 'switcher',
			togglerSelector: 'a',
			elementSelector: 'div.tab',
			elementPrefix: '#page-',
			element: '#main'
		},
		_DEBUG = false;

	function Plugin(container, options) {
		this.container = jQuery(container);
		this.options   = jQuery.extend({}, defaults, options);

		this._defaults = defaults;
		this._name     = pluginName;

		this.togglers  = [];
		this.elements  = [];
		this.current   = null;

		this.init();
	};

	Plugin.prototype = {
		init: function() {
			var self = this;

			this.togglers = this.container.find(this.options.togglerSelector);
			this.elements = jQuery(this.options.element).find(this.options.elementSelector);

			if ((this.togglers.length == 0) || (this.togglers.length != this.elements.length)) {
				return;
			}

			this.hideAll();

			this.togglers.on('click', function(e){
				e.preventDefault();

				self.display(jQuery(this).attr('id'));
			});

			var first = document.location.hash.substring(1);

			if (typeof(Storage) !== 'undefined') {
				if (!first) {
					first = localStorage.getItem(this.options.elementPrefix+'active');
				}
			}

			if (!first || !this.has(first)) {
				first = jQuery(this.togglers[0]).attr('id');
			}
			this.display(first);

			if (document.location.hash) {
				setTimeout(function() {
					window.scrollTo(0, 0);
				}, 1);
			}
		},

		has: function(togglerID) {
			var toggler = jQuery('#' + togglerID),
				element = jQuery(this.options.elementPrefix+togglerID);

			if (toggler.length && element.length) {
				return true;
			}

			return false;
		},

		display: function(togglerID) {
			var toggler = jQuery('#' + togglerID),
				element = jQuery(this.options.elementPrefix+togglerID);

			if (toggler == null || element == null || toggler == this.current) {
				return this;
			}

			if (this.current != null) {
				this.hide(jQuery(this.options.elementPrefix+this.current));
				jQuery('#' + this.current).removeClass('active');
			}

			this.show(element);
			toggler.addClass('active');

			this.current = toggler.attr('id');

			if (typeof(Storage) !== 'undefined') {
				localStorage.setItem(this.options.elementPrefix+'active', this.current);
			}
			document.location.hash = this.current;
			jQuery(window).scrollTop(0);
		},

		hide: function(element) {
			element.hide();
			this.options.onHide();
		},

		hideAll: function() {
			this.elements.hide();
			this.togglers.removeClass('active');
		},

		show: function (element) {
			element.show();
			this.options.onShow();
		}
	};

	jQuery.fn[pluginName] = function(options) {
		return this.each(function() {
			if (!jQuery.data(this, 'plugin_' + pluginName)) {
				jQuery.data(this, 'plugin_' + pluginName, new Plugin(this, options));
			}
		});
	};

})(jQuery, window, document);