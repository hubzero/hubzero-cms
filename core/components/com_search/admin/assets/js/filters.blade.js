/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

var fbInstances = [];
var colors = ['#f0e7f4', '#e9f1fa', '#f9fbe5', '#ecf8f6', '#fbf7dc', '#FEEFB2', '#FFDDDC', '#DEEDFF', '#DCCFFC'];
var current = 0;

document.addEventListener('DOMContentLoaded', function () {
	var itemForm = document.getElementById('item-form');
	var addPageTab = document.getElementById('add-page-tab');
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

	// Initialize tabs if jQuery UI is available
	if (typeof jQuery !== 'undefined' && jQuery.fn.tabs) {
		jQuery('#item-form').tabs();
	}

	if (itemForm) {
		itemForm.addEventListener('click', function (e) {
			var deleteBtn = e.target.closest('.delete-page');
			if (!deleteBtn) {
				return;
			}
			e.preventDefault();

			var par = deleteBtn.parentNode.parentNode.parentNode;
			var tabIdStr = par.getAttribute('id');
			var fbEditors = document.querySelectorAll('.fb-editor');
			var idx = Array.prototype.indexOf.call(fbEditors, par);

			var hrefLink = document.querySelector("a[href='#" + tabIdStr + "']");
			if (hrefLink) {
				var li = hrefLink.closest('li');
				if (li) {
					li.remove();
				}
			}
			par.remove();

			var tabsEl = document.getElementById('tabs');
			var tabCount = tabsEl ? tabsEl.children.length : 0;

			if (typeof jQuery !== 'undefined' && jQuery.fn.tabs) {
				jQuery('#item-form').tabs('refresh');
				jQuery('#item-form').tabs('option', 'active', tabCount - 2);
			}

			fbInstances.splice(idx, 1);
		});

		itemForm.addEventListener('change', function (e) {
			if (e.target.classList.contains('option-dependents')) {
				setDependentColors(e.target);
			}
		});
		itemForm.addEventListener('focus', function (e) {
			if (e.target.classList.contains('option-dependents')) {
				setDependentColors(e.target);
			}
		}, true);
		itemForm.addEventListener('blur', function (e) {
			if (e.target.classList.contains('option-dependents')) {
				setDependentColors(e.target);
			}
		}, true);
	}

	var options1 = JSON.parse(JSON.stringify(options));
	var formSchema = document.getElementById('form-schema');
	if (formSchema) {
		options1.defaultFields = formSchema.value;
	}

	var page1 = document.getElementById('page-1');
	if (page1 && typeof jQuery !== 'undefined' && jQuery.fn.formBuilder) {
		var fb = jQuery('#page-1').formBuilder(options1);

		fb.promise.then(function () {
			var dependents = document.querySelectorAll('.option-dependents');
			for (var i = 0; i < dependents.length; i++) {
				setDependentColors(dependents[i]);
			}
		});

		fbInstances.push(fb);
	}
});

function setDependentColors(de) {
	if (de.value) {
		var clr = de.getAttribute('data-color');

		if (!clr) {
			clr = colors[current];
			current++;
			current = current >= colors.length ? 0 : current;

			de.style.backgroundColor = clr;
			de.setAttribute('data-color', clr);
		}

		de.setAttribute('data-fields', de.value);

		var fields = de.value.split(',');
		for (var i = 0; i < fields.length; i++) {
			var field = fields[i].replace(/\s/, '');
			var fld = document.querySelector('.field-' + field + '-preview');
			if (fld) {
				var li = fld.closest('li.form-field');
				if (li) {
					li.style.backgroundColor = clr;
				}
			}
		}
	} else if (de.getAttribute('data-color')) {
		var dataFields = de.getAttribute('data-fields');
		if (dataFields) {
			var fields = dataFields.split(',');
			for (var i = 0; i < fields.length; i++) {
				var field = fields[i].replace(/\s/, '');
				var fld = document.querySelector('.field-' + field + '-preview');
				if (fld) {
					var li = fld.closest('li.form-field');
					if (li) {
						li.style.backgroundColor = '#fff';
					}
				}
			}
		}

		de.setAttribute('data-fields', '');
		de.setAttribute('data-color', '');
		de.style.backgroundColor = '#fff';
	}
}

function submitbutton(pressbutton) {
	var form = document.getElementById('adminForm');

	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}

	var sbmt = true;
	var headerInputs = document.querySelectorAll('.fb-editor .form-page-header input');
	for (var i = 0; i < headerInputs.length; i++) {
		if (!headerInputs[i].value) {
			alert('Please provide a title for each page.');
			sbmt = false;
			break;
		}
	}
	if (!sbmt) {
		return;
	}

	var allData = fbInstances.map(function (fb) {
		return fb.formData;
	});

	var formSchemaEl = document.getElementById('form-schema');
	if (formSchemaEl) {
		formSchemaEl.value = allData;
	}

	submitform(pressbutton);
}
