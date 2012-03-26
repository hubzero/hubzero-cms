/**
 * @package     hubzero-cms
 * @file        components/com_contribute/contribute.js
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

//-----------------------------------------------------------
// Edit in place script for attachments
//-----------------------------------------------------------

var eip = new Class({

	initialize: function(els, action, params, options) {
		// Handle array of elements or single element
		if ($type(els) == 'array') {
			els.each(function(el){
				this.prepForm(el);
			}.bind(this));
		} else if ($type(els) == 'element') {
			this.prepForm(els);
		} else {
			return;
		}

		// Store the action (path to file) and params
		this.action = action;
		this.params = params;

		// Default options
		this.options = Object.extend({
			overCl: 'over',
			hiddenCl: 'hidden',
			editableCl: 'editable',
			textareaCl: 'textarea'
		}, options || {} );
	},

	prepForm: function(el) {
		var obj = this;
		el.addEvents({
			'mouseover': function(){this.addClass(obj.options.overCl);},
			'mouseout': function(){this.removeClass(obj.options.overCl);},
			'click': function(){obj.showForm(this);}
		});

	},

	showForm: function(el) {
		// Get the name (target) and id from your element
		var classes = el.getProperty('class').split(" ");
		for (i=classes.length-1;i>=0;i--) {
			if (classes[i].contains('item:')) {
				var target = classes[i].split(":")[1];
			} else if (classes[i].contains('id:')) {
				var id = classes[i].split(":")[1];
			}
		}

		// Hide your target element
		el.addClass(this.options.hiddenCl);

		// If the form exists already, let's show that
		if (el.form) {
			el.form.removeClass(this.options.hiddenCl);
			el.form[target].focus();
			return;
		}

		// Create new form
		var form = new Element('form', {
			'id': 'form_' + el.getProperty('id'),
			'action': this.action,
			'class': this.options.editableCl
		});

		// Store new form in the element
		el.form = form;

		// Create a textarea or input for user
		if (el.hasClass(this.options.textareaCl)) {
			var input = new Element('textarea', {
				'name': target
			}).appendText(el.innerHTML).injectInside(form);
		} else {
			var input = new Element('input', {
				'name': target,
				'value': el.innerHTML
			}).injectInside(form);
			input.style.width = '120px';
		}

		// Need this to pass to the buttons
		var obj = this;

		// Add a submit button
		new Element('input', {
			'type': 'submit',
			'value': 'save',
			'events': {
				'click': function(evt){
					(new Event(evt)).stop();
					el.empty();
					el.appendText('saving...');
					obj.hideForm(form, el);
					form.send({update: el});
				}
			}
		}).injectInside(form);

		// Add a cancel button
		new Element('input', {
			'type': 'button',
			'value': 'cancel',
			'events': {
				'click': function(form, el){
					obj.hideForm(form, el);
				}.pass([form, el])
			}
		}).injectInside(form);

		// For every param, add a hidden input
		for (param in this.params) {
			new Element('input', {
				'type': 'hidden',
				'name': param,
				'value': this.params[param]
			}).injectInside(form);
		}

		//
		new Element('input', {
			'type': 'hidden',
			'name': 'id',
			'value': id
		}).injectInside(form);

		// Add the form after the target element
		form.injectAfter(el);

		// Focus on the input
		input.focus();
	},

	hideForm: function(form, el) {
		form.addClass(this.options.hiddenCl);
		el.removeClass(this.options.hiddenCl);
	}
});

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//-----------------------------------------------------------
//  Highlight table rows when clicking checkbox
//-----------------------------------------------------------
HUB.Contribute = {
	initialize: function() {
		if ($('license-preview')) {
			$('license-preview').setStyles({'display':'block'});
		}
		if ($('license')) {
			if ($('license').value == 'custom') {
				$('license-text').setStyles({'display':'inline-block'});
				$('license-preview').setStyles({'display':'none'});
			}
			$('license').addEvent('change', function() {
				if ($(this).value != '') {
					$('license-preview').innerHTML = $('license-' + $(this).value).value;
					if ($('license-text')) {
						if ($(this).value == 'custom') {
							//$('license-preview').innerHTML = '<textarea name="license-text" cols="75" rows="10">' + $('license-' + $(this).value).value + '</textarea>';
							$('license-text').setStyles({'display':'inline-block'});
							$('license-preview').setStyles({'display':'none'});
						} else {
							$('license-text').setStyles({'display':'none'});
							$('license-preview').setStyles({'display':'block'});
						}
					}
				} else {
					$('license-preview').innerHTML = 'License preview.';
				}
			});
		}
		
		new eip($$('.ftitle'), 'index.php', {option: 'com_contribute', task: 'rename', no_html: 1});
	}
}

//-----------------------------------------------------------

window.addEvent('domready', HUB.Contribute.initialize);

