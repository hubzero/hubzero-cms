/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(function(){
	$('#add-filter').on('click', function(e){
		e.preventDefault();
		var filterName = $('[name="add-filter"]').val();
		var filterType = $('select[name="filter-type"]').val();
		$('input[name="add-filter"]').val('');
		var values = {'type': filterType};
		var newFilter = addNewFilter(filterName, values);
		$('#page-1').append(newFilter);
	});
	$('#page-1').on('click', '.add-options', function(e){
		e.preventDefault();
		list.addOption($(this), true);
	});
	$('#page-1').on('click', '.editable', function(e){
		var filterType = getType($(this));
		$('.editable').not(this).each(function(index){
			setReadonlyState($(this));
		});
		setEditableState($(this));
	});
	$('#page-1').on('blur', '.filter-value', function(e){
		e.preventDefault();
		var filterType = getType($(this));
		var parentContainer = $(this).closest('.editable');
		setReadonlyState(parentContainer);
	});
	$('#page-1').on('click', '.remove-filter', function(e){
		e.preventDefault();
		e.stopPropagation();
		$(this).closest('article').remove();
	});
	renderCurrentFilters();
	$('.articles-container').sortable();
	$('.options-list').sortable();
});
function renderCurrentFilters(){
	var currentFilters = $('input[name="filter-schema"]').val();
	currentFilters = JSON.parse(currentFilters);
	$.each(currentFilters, function(index, values){
		var newFilter = addNewFilter(index, values);
		$('#page-1 .articles-container').append(newFilter);
	});
}

function addNewFilter(filterName, values){
	var filterType = values['type'];
	var filterContainer = $('<article>');
	var headerContainer = $('<header class="editable">');
	var label = values['label'] === undefined ? filterName : values['label'];
	headerContainer.append('<h3 class="filter-label">' + label + '</h3>');
	headerContainer.append('<a href="#" class="remove-filter">Remove Filter</a>');
	headerContainer.append('<input type="hidden" class="filter-value" name="filters[' + filterName + '][label]" value="' + label + '"/>');
	filterContainer.append(headerContainer);
	if (window[filterType] && window[filterType].hasOwnProperty('addOptionContainer'))
	{
		var optionContainer = window[filterType].addOptionContainer(filterName, values);
		filterContainer.append(optionContainer);
	}
	var hiddenType = $('<input>');
	hiddenType.attr('type', 'hidden');
	hiddenType.attr('name', 'filters[' + filterName + '][type]');
	hiddenType.addClass('filter-type');
	hiddenType.val(filterType);
	filterContainer.append(hiddenType);
	return filterContainer;
}
function getType(item){
	var parentContainer = item.closest('article');
	var itemType = parentContainer.find('.filter-type').val();
	return itemType;
}
function setEditableState(option){
	var filterInput = option.find('.filter-value');
	var filterLabel = filterInput.val();
	filterInput.attr('type', 'text');
	filterInput.siblings('.filter-label').hide();
}
function setReadonlyState(option){
	var filterInput = option.find('.filter-value');
	var filterLabel = filterInput.val();
	if (filterLabel.length == 0)
	{
		option.remove();
	}
	filterInput.attr('type', 'hidden');
	filterInput.siblings('.filter-label').text(filterLabel).show();
}
var list = {
	addOptionContainer: function(filterName, values){
		var optionsContainer = $('<section class="options">');
		optionsContainer.append('<ul class="options-list"></ul>');
		var button = $('<button class="add-options" data-filter="' + filterName + '"></button>');
		button.text('Add options');
		optionsContainer.append(button);
		if (values['options'] !== undefined)
		{
			$.each(values['options'], function(index, value){
				list.addOption(button, false, value);
			});
		}
		return optionsContainer;
	},
	addOption: function(optionButton, newOption, value){
		var filter = optionButton.data('filter');
		var optionContainer = $('<li class="editable">');
		var optionInput = $('<input class="filter-value" type="hidden" name="filters[' + filter + '][options][]" />');
		var optionLabel = $('<h4 class="filter-label"></h4>');
		if (value !== undefined)
		{
			optionInput.val(value);
			optionLabel.text(value);
		}
		var optionsList = optionButton.siblings('.options-list'); 
		optionContainer.append(optionInput);
		optionContainer.append(optionLabel);
		if (newOption === true)
		{
			optionLabel.hide();
			optionInput.attr('type', 'text');
			optionsList.children('.editable').each(function(index){
				setReadonlyState($(this));
			});
		}
		optionsList.append(optionContainer);
	}
}

var daterange = {
	addOptionContainer: function(filterName, values){
		var params = values['params'];
		if (params !== undefined)
		{
			var minDateValue = params['minDate'];
			var maxDateValue = params['maxDate'];
		}
		var optionsContainer = $('<section class="options">');
		var minDateContainer = $('<div class="input-wrap col span6"><label>Minimum Date</label></div>');
		var minDate = $('<input class="calendar-field" type="text" name="filters[' + filterName + '][params][minDate]" value="" />');
		if (minDateValue !== undefined)
		{
			minDate.val(minDateValue);
		}
		minDate.datepicker({
			dateFormat: 'yy-mm-dd'
		});
		var maxDateContainer = $('<div class="input-wrap col span6"><label>Maximum Date</label></div>');
		var maxDate = $('<input class="calendar-field" type="text" value="" name="filters[' + filterName + '][params][maxDate]" />');
		if (maxDateValue !== undefined)
		{
			maxDate.val(maxDateValue);
		}
		maxDate.datepicker({
			dateFormat: 'yy-mm-dd'
		});
		minDateContainer.append(minDate);	
		maxDateContainer.append(maxDate);
		optionsContainer.append(minDateContainer).append(maxDateContainer);
		return optionsContainer;
	}
}

var textfield = {
	addOptionContainer: function(filterName, values){
		var optionsContainer = $('<section class="options">');
		var textContainer = $('<div class="input-wrap col span12"></div>');
		var textField = $('<input type="text" name="filters[' + filterName + '][default]" value="" placeholder="Filter..."/>');
		textContainer.append(textField);	
		optionsContainer.append(textContainer);
		return optionsContainer;
	}
}
