/**
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Unobtrusive Form Validation library
 *
 * Inspired by: Chris Campbell <www.particletree.com>
 *
 * @package		Joomla.Framework
 * @subpackage	Forms
 * @since		1.5
 */
//var JFormValidator = new Class({
var JFormValidator = function() {
	this.initialize = function()
	{
		// Initialize variables
		this.handlers	= Object();
		this.custom		= Object();

		// Default handlers
		this.setHandler('username',
			function (value) {
				regex = new RegExp("[\<|\>|\"|\'|\%|\;|\(|\)|\&]", "i");
				return !regex.test(value);
			}
		);

		this.setHandler('password',
			function (value) {
				regex=/^\S[\S ]{2,98}\S$/;
				return regex.test(value);
			}
		);

		this.setHandler('numeric',
			function (value) {
				regex=/^(\d|-)?(\d|,)*\.?\d*$/;
				return regex.test(value);
			}
		);

		this.setHandler('email',
			function (value) {
				regex=/^[a-zA-Z0-9._-]+(\+[a-zA-Z0-9._-]+)*@([a-zA-Z0-9.-]+\.)+[a-zA-Z0-9.-]{2,4}$/;
				return regex.test(value);
			}
		);

		// Attach to forms with class 'form-validate'
		var forms = $('form.form-validate');
		forms.each(function(i, form){ this.attachToForm(form); }, this);
	};

	this.setHandler = function(name, fn, en)
	{
		en = (en == '') ? true : en;
		this.handlers[name] = { enabled: en, exec: fn };
	};

	this.attachToForm = function(form)
	{
		// Iterate through the form object and attach the validate method to all input fields.
		form.find('input,textarea,select,button').each(function(i, el){
			el = $(el);
			if (el.hasClass('required')) {
				el.attr('aria-required', 'true');
				el.attr('required', 'required');
			}
			if ((el.prop('tagName') == 'input' || $(el).prop('tagName') == 'button') && $(el).attr('type') == 'submit') {
				if (el.hasClass('validate')) {
					el.on('click', function(){return document.formvalidator.isValid(this.form);});
				}
			} else {
				el.on('blur', function(){return document.formvalidator.validate(this);});
				if (el.hasClass('validate-email') && this.inputemail) {
					el.attr('type') = 'email';
				}
			}
		});
	};

	this.inputemail = function() 
	{
		var i = document.createElement("input");
		i.setAttribute("type", "email");
		return i.type !== "text";
	};

	this.validate = function(el)
	{
		el = $(el);

		// Ignore the element if its currently disabled, because are not submitted for the http-request. For those case return always true.
		if (el.attr('disabled') && !(el.hasClass('required'))) {
			this.handleResponse(true, el);
			return true;
		}

		// If the field is required make sure it has a value
		if (el.hasClass('required')) {
			if (el.prop('tagName')=='fieldset' && (el.hasClass('radio') || el.hasClass('checkboxes'))) {
				for(var i=0;;i++) {
					if ($(el.attr('id')+i)) {
						if ($(el.attr('id')+i).checked) {
							break;
						}
					}
					else {
						this.handleResponse(false, el);
						return false;
					}
				}
			}
			else if (!(el.val())) {
				this.handleResponse(false, el);
				return false;
			}
		}

		// Only validate the field if the validate class is set
		var handler = (el.className && el.className.search(/validate-([a-zA-Z0-9\_\-]+)/) != -1) ? el.className.match(/validate-([a-zA-Z0-9\_\-]+)/)[1] : "";
		if (handler == '') {
			this.handleResponse(true, el);
			return true;
		}

		// Check the additional validation types
		if ((handler) && (handler != 'none') && (this.handlers[handler]) && el.get('value')) {
			// Execute the validation handler and return result
			if (this.handlers[handler].exec(el.get('value')) != true) {
				this.handleResponse(false, el);
				return false;
			}
		}

		// Return validation state
		this.handleResponse(true, el);
		return true;
	};

	this.isValid = function(form)
	{
		var valid = true;

		// Validate form fields
		var elements = form.find('fieldset'); //.concat(Array.from(form.elements));
		for (var i=0;i < elements.length; i++) {
			if (this.validate(elements[i]) == false) {
				valid = false;
			}
		}

		// Run custom form validators if present
		/*new Hash(this.custom).each(function(validator){
			if (validator.exec() != true) {
				valid = false;
			}
		});*/

		return valid;
	};

	this.handleResponse = function(state, el)
	{
		// Find the label object for the given field if it exists
		if (!(el.labelref)) {
			var labels = $('label');
			labels.each(function(label){
				label = $(label);
				if (label.attr('for') == el.attr('id')) {
					el.labelref = label;
				}
			});
		}

		// Set the element and its label (if exists) invalid state
		if (state == false) {
			el.addClass('invalid');
			el.attr('aria-invalid', 'true');
			if (el.labelref) {
				$(el.labelref).addClass('invalid');
				$(el.labelref).attr('aria-invalid', 'true');
			}
		} else {
			el.removeClass('invalid');
			el.attr('aria-invalid', 'false');
			if (el.labelref) {
				$(el.labelref).removeClass('invalid');
				$(el.labelref).attr('aria-invalid', 'false');
			}
		}
	};
};

document.formvalidator = null;
jQuery(document).ready(function($){
	document.formvalidator = new JFormValidator();
});
