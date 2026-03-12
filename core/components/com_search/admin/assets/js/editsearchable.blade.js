/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

document.addEventListener('DOMContentLoaded', function () {
	var addFilterBtn = document.getElementById('add-filter');
	var page1 = document.getElementById('page-1');

	if (addFilterBtn) {
		addFilterBtn.addEventListener('click', function (e) {
			e.preventDefault();
			var filterNameInput = document.querySelector('[name="add-filter"]');
			var filterTypeSelect = document.querySelector('select[name="filter-type"]');
			var filterName = filterNameInput ? filterNameInput.value : '';
			var filterType = filterTypeSelect ? filterTypeSelect.value : '';
			if (filterNameInput) {
				filterNameInput.value = '';
			}
			var values = {'type': filterType};
			var newFilter = addNewFilter(filterName, values);
			if (page1) {
				page1.appendChild(newFilter);
			}
		});
	}

	if (page1) {
		page1.addEventListener('click', function (e) {
			var target = e.target;

			// Handle add-options click
			if (target.classList.contains('add-options') || target.closest('.add-options')) {
				e.preventDefault();
				var btn = target.classList.contains('add-options') ? target : target.closest('.add-options');
				list.addOption(btn, true);
				return;
			}

			// Handle editable click
			var editable = target.closest('.editable');
			if (editable) {
				var allEditables = page1.querySelectorAll('.editable');
				for (var i = 0; i < allEditables.length; i++) {
					if (allEditables[i] !== editable) {
						setReadonlyState(allEditables[i]);
					}
				}
				setEditableState(editable);
				return;
			}

			// Handle remove-filter click
			if (target.classList.contains('remove-filter') || target.closest('.remove-filter')) {
				e.preventDefault();
				e.stopPropagation();
				var removeBtn = target.classList.contains('remove-filter') ? target : target.closest('.remove-filter');
				var article = removeBtn.closest('article');
				if (article) {
					article.remove();
				}
				return;
			}
		});

		page1.addEventListener('blur', function (e) {
			var target = e.target;
			if (target.classList.contains('filter-value')) {
				e.preventDefault();
				var parentContainer = target.closest('.editable');
				if (parentContainer) {
					setReadonlyState(parentContainer);
				}
			}
		}, true);
	}

	renderCurrentFilters();
});

function renderCurrentFilters() {
	var schemaInput = document.querySelector('input[name="filter-schema"]');
	if (!schemaInput) {
		return;
	}
	var currentFilters = schemaInput.value;
	currentFilters = JSON.parse(currentFilters);
	var container = document.querySelector('#page-1 .articles-container');
	for (var index in currentFilters) {
		if (currentFilters.hasOwnProperty(index)) {
			var newFilter = addNewFilter(index, currentFilters[index]);
			if (container) {
				container.appendChild(newFilter);
			}
		}
	}
}

function addNewFilter(filterName, values) {
	var filterType = values['type'];

	var filterContainer = document.createElement('article');

	var headerContainer = document.createElement('header');
	headerContainer.className = 'editable';

	var label = values['label'] === undefined ? filterName : values['label'];

	var h3 = document.createElement('h3');
	h3.className = 'filter-label';
	h3.textContent = label;
	headerContainer.appendChild(h3);

	var removeLink = document.createElement('a');
	removeLink.href = '#';
	removeLink.className = 'remove-filter';
	removeLink.textContent = 'Remove Filter';
	headerContainer.appendChild(removeLink);

	var hiddenInput = document.createElement('input');
	hiddenInput.type = 'hidden';
	hiddenInput.className = 'filter-value';
	hiddenInput.name = 'filters[' + filterName + '][label]';
	hiddenInput.value = label;
	headerContainer.appendChild(hiddenInput);

	filterContainer.appendChild(headerContainer);

	if (window[filterType] && window[filterType].hasOwnProperty('addOptionContainer')) {
		var optionContainer = window[filterType].addOptionContainer(filterName, values);
		filterContainer.appendChild(optionContainer);
	}

	var hiddenType = document.createElement('input');
	hiddenType.type = 'hidden';
	hiddenType.name = 'filters[' + filterName + '][type]';
	hiddenType.className = 'filter-type';
	hiddenType.value = filterType;
	filterContainer.appendChild(hiddenType);

	return filterContainer;
}

function getType(item) {
	var parentContainer = item.closest('article');
	var typeInput = parentContainer ? parentContainer.querySelector('.filter-type') : null;
	return typeInput ? typeInput.value : '';
}

function setEditableState(option) {
	var filterInput = option.querySelector('.filter-value');
	if (!filterInput) {
		return;
	}
	filterInput.type = 'text';
	var filterLabel = option.querySelector('.filter-label');
	if (filterLabel) {
		filterLabel.style.display = 'none';
	}
}

function setReadonlyState(option) {
	var filterInput = option.querySelector('.filter-value');
	if (!filterInput) {
		return;
	}
	var filterLabel = filterInput.value;
	if (filterLabel.length == 0) {
		option.remove();
		return;
	}
	filterInput.type = 'hidden';
	var labelEl = option.querySelector('.filter-label');
	if (labelEl) {
		labelEl.textContent = filterLabel;
		labelEl.style.display = '';
	}
}

var list = {
	addOptionContainer: function (filterName, values) {
		var optionsContainer = document.createElement('section');
		optionsContainer.className = 'options';

		var ul = document.createElement('ul');
		ul.className = 'options-list';
		optionsContainer.appendChild(ul);

		var button = document.createElement('button');
		button.className = 'add-options';
		button.setAttribute('data-filter', filterName);
		button.textContent = 'Add options';
		optionsContainer.appendChild(button);

		if (values['options'] !== undefined) {
			for (var i = 0; i < values['options'].length; i++) {
				list.addOption(button, false, values['options'][i]);
			}
		}
		return optionsContainer;
	},
	addOption: function (optionButton, newOption, value) {
		var filter = optionButton.getAttribute('data-filter');

		var optionContainer = document.createElement('li');
		optionContainer.className = 'editable';

		var optionInput = document.createElement('input');
		optionInput.className = 'filter-value';
		optionInput.type = 'hidden';
		optionInput.name = 'filters[' + filter + '][options][]';

		var optionLabel = document.createElement('h4');
		optionLabel.className = 'filter-label';

		if (value !== undefined) {
			optionInput.value = value;
			optionLabel.textContent = value;
		}

		var optionsList = optionButton.parentNode.querySelector('.options-list');

		optionContainer.appendChild(optionInput);
		optionContainer.appendChild(optionLabel);

		if (newOption === true) {
			optionLabel.style.display = 'none';
			optionInput.type = 'text';
			var editables = optionsList.querySelectorAll('.editable');
			for (var i = 0; i < editables.length; i++) {
				setReadonlyState(editables[i]);
			}
		}

		optionsList.appendChild(optionContainer);
	}
};

var daterange = {
	addOptionContainer: function (filterName, values) {
		var params = values['params'];
		var minDateValue, maxDateValue;
		if (params !== undefined) {
			minDateValue = params['minDate'];
			maxDateValue = params['maxDate'];
		}

		var optionsContainer = document.createElement('section');
		optionsContainer.className = 'options';

		var minDateContainer = document.createElement('div');
		minDateContainer.className = 'input-wrap col span6';
		var minLabel = document.createElement('label');
		minLabel.textContent = 'Minimum Date';
		minDateContainer.appendChild(minLabel);

		var minDate = document.createElement('input');
		minDate.className = 'calendar-field';
		minDate.type = 'date';
		minDate.name = 'filters[' + filterName + '][params][minDate]';
		minDate.value = minDateValue !== undefined ? minDateValue : '';
		minDateContainer.appendChild(minDate);

		var maxDateContainer = document.createElement('div');
		maxDateContainer.className = 'input-wrap col span6';
		var maxLabel = document.createElement('label');
		maxLabel.textContent = 'Maximum Date';
		maxDateContainer.appendChild(maxLabel);

		var maxDate = document.createElement('input');
		maxDate.className = 'calendar-field';
		maxDate.type = 'date';
		maxDate.name = 'filters[' + filterName + '][params][maxDate]';
		maxDate.value = maxDateValue !== undefined ? maxDateValue : '';
		maxDateContainer.appendChild(maxDate);

		optionsContainer.appendChild(minDateContainer);
		optionsContainer.appendChild(maxDateContainer);

		return optionsContainer;
	}
};

var textfield = {
	addOptionContainer: function (filterName, values) {
		var optionsContainer = document.createElement('section');
		optionsContainer.className = 'options';

		var textContainer = document.createElement('div');
		textContainer.className = 'input-wrap col span12';

		var textField = document.createElement('input');
		textField.type = 'text';
		textField.name = 'filters[' + filterName + '][default]';
		textField.value = '';
		textField.placeholder = 'Filter...';

		textContainer.appendChild(textField);
		optionsContainer.appendChild(textContainer);

		return optionsContainer;
	}
};
