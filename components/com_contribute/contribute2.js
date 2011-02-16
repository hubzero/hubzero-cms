//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

HUB.ContributeEnhancement = {

	hideButtons: function()
	{
		$jQ('#hubfancy-filebutton').hide();
		$jQ('#hubfancy-linkbutton').hide();
	},
	
	showButtons: function()
	{
		$jQ('#hubfancy-filebutton').show();
		$jQ('#hubfancy-linkbutton').show();
	},

	showLinkForm: function() 
	{
		this.hideButtons();
		$jQ('#link-attachments-form').show();
	},
	
	
	showFileForm: function() 
	{
		this.hideButtons();
		$jQ('#attachments-form').show();
	},
	
	cancel: function()
	{
		$jQ('#attachments-form').hide();
		$jQ('#link-attachments-form').hide();
		this.showButtons();
	},
	
	//allow users to edit description
	initialize: function() {
	
		new deip($$('.fdesc'), 'index.php', {option: 'com_contribute', task: 'redescribe', no_html: 1});
	}
}

//-----------------------------------------------------------
// Edit in place script for attachments
//-----------------------------------------------------------

var deip = new Class({

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
		//form.set('html','<fieldset>');

		// Store new form in the element
		el.form = form;
		
		
		
		var set = new Element('fieldset').injectInside(form);

		// Create a textarea or input for user
		var input = new Element('textarea', {
				'name': target
		}).appendText(el.innerHTML).injectInside(set);
	

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
					for ( instance in CKEDITOR.instances )
    CKEDITOR.instances[instance].updateElement();
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
		//form.set('html', '</fieldset>');
		form.injectAfter(el);
	
		CKEDITOR.replace( 'linkdescedit' , {
			        toolbar : 'NEESBasicDescription',
			        height:"50", width:"100%",
		});
		// Focus on the input
		//input.focus();
	},

	hideForm: function(form, el) {
		form.addClass(this.options.hiddenCl);
		el.removeClass(this.options.hiddenCl);
	}
	

});


//-----------------------------------------------------------

window.addEvent('domready', HUB.ContributeEnhancement.initialize);
