/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var fbInstances = [];
var colors = ['#f0e7f4', '#e9f1fa', '#f9fbe5', '#ecf8f6', '#fbf7dc', '#FEEFB2', '#FFDDDC', '#DEEDFF', '#DCCFFC'];
var current = 0;

jQuery(document).ready(function($){
	var $fbPages = $(document.getElementById("item-form"));
	var addPageTab = document.getElementById("add-page-tab");
	var extraFields = { 
		'access': {
			label: 'Privacy',
			options: {
				'0': 'public',
				'1': 'registered',
				'2': 'private'
			},
			name: 'access'
		},
		'id': {
			type: 'hidden',
			label: '&nbsp;'
		}
	};

	var options = {
		disableFields: ['autocomplete', 'file', 'button', 'header', 'hidden'],
		disabledActionButtons: ['clear', 'data', 'save'],
		editOnAdd: true,
		//disableInjectedStyle: true,
		disabledAttrs: ['inline', 'style', 'access', 'className', 'subtype'],
		typeUserAttrs: {
			'radio-group': extraFields, 
			'checkbox-group': extraFields, 
			'textarea': extraFields, 
			'select': extraFields, 
			'date': extraFields, 
			'paragraph': extraFields,
			'hidden': extraFields, 
			'number': extraFields, 
			'text': extraFields 
		}
	};

	$fbPages.tabs();

	$('#item-form').on('click', '.delete-page', function (e){
		e.preventDefault();

		//$fbPages.tabs('remove', );

		var par = $($(this).parent().parent().parent());
		var tabIdStr = par.attr('id');
		var idx = $('.fb-editor').index(par);

		var hrefStr = "a[href='#" + tabIdStr + "']";
		$(hrefStr).closest("li").remove();
		par.remove();

		var tabCount = document.getElementById("tabs").children.length;

		$fbPages.tabs("refresh");
		$fbPages.tabs("option", "active", tabCount - 2);

		fbInstances.splice(idx, 1);
	});


		var options1 = jQuery.extend(true, {}, options);

		options1.defaultFields = $('#form-schema').val();
		fb = $('#page-1').formBuilder(options1);

		fb.promise.then(function(formbuilder) {
			$('.option-dependents').each(function (i, el){
				var de = $(el);

				setDependentColors(de);
			});
		});

		fbInstances.push(fb);


	$('#item-form').on('change focus blur', '.option-dependents', function (e){
		var de = $(this);

		setDependentColors(de);
	});
});

function setDependentColors(de)
{
	if (de.val()) {
		var clr = de.attr('data-color');

		if (!clr) {
			clr = colors[current]; //colors[Math.floor(Math.random() * colors.length)];
			current++;
			current = current >= colors.length ? 0 : current;

			de.css('background-color', clr);
			de.attr('data-color', clr);
		}

		de.attr('data-fields', de.val());

		var fields = de.val().split(',');
		for (var i = 0; i < fields.length; i++)
		{
			var field = fields[i].replace(/\s/, '');
			var fld = $('.field-' + field + '-preview');
			if (fld.length) {
				var li = $(fld).closest('li.form-field').css('background-color', clr);
			}
		}
	} else if (de.attr('data-color')) {
		var fields = de.attr('data-fields').split(',');
		for (var i = 0; i < fields.length; i++)
		{
			var field = fields[i].replace(/\s/, '');
			var fld = $('.field-' + field + '-preview');
			if (fld.length) {
				var li = $(fld).closest('li.form-field').css('background-color', '#fff');
			}
		}

		de.attr('data-fields', '');
		de.attr('data-color', '');
		de.css('background-color', '#fff');
	}
}

function submitbutton(pressbutton)
{
	var form = document.getElementById('adminForm');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	var sbmt = true;
	$('.fb-editor .form-page-header input').each(function (i, el){
		if (!$(el).val()) {
			alert('Please provide a title for each page.');
			sbmt = false;
		}
	});
	if (!sbmt) {
		return;
	}

	var allData = fbInstances.map(function(fb) {
		return fb.formData;
	});

	$('#form-schema').val(allData);

	submitform(pressbutton);
}
