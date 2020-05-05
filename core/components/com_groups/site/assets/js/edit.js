/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$(function(){
	$('input[data-dependents]').each(function(index, element){
		checkDependentDisplay(element, false);	
		var fieldName = $(element).attr('name');
		var selections = $('[name="' + fieldName + '"]');
		selections.each(function(index, element){
			$(element).on('click', function(e){
				$(selections).each(function(index, element){
					checkDependentDisplay(element, true);
				});
			});
		});

	});

	$('option[data-dependents]').each(function(index, element){
		checkDependentDisplay(element, false);
	});

	$('select').each(function(index, element){
		if ($(element).has('option[data-dependents]'))
		{
			$(element).on('change', function(e){
				var options = $(this).find('option');
				options.each(function(index, element){
					checkDependentDisplay(element, true);
				});
			});
		}
	});
});

function checkDependentDisplay(element, recursive, state){
	if (state === undefined || state === true)
	{
		state = $(element).is('input') ? element.checked : element.selected;
	}
	var dependent = getDependentName(element);
	var displayState = state ? "show" : "hide";
	$(dependent).each(function(index, child){
		toggleWithLabel(child, displayState);
		if (recursive)
		{
			checkDependentDisplay(child, recursive, state);
		}
	});
}

function getDependentName(element){
	var collection = $(element).is('option') ? $(element).parent('select').attr('name') : $(element).attr('name');
	var dependentName = $(element).data('dependents');
	var collectionPos = collection.indexOf('[');
	if (collectionPos !== -1)
	{
		collection = collection.substring(0, collectionPos);
		dependentName = collection + '[' + dependentName + ']';
	}
	dependentName = '[name="' + dependentName + '"]';
	return dependentName;
}

function toggleWithLabel(element, toggle){
	var dependentId = $(element).attr('id');
	var parentContainer = $(element).parent('li').closest('fieldset');
	var parentId = $(parentContainer).attr('id');
	if (toggle == 'show')
	{
		$('label[for="' + dependentId + '"]').show();
		$(element).show();
		parentContainer.show();
		$('label[for="' + parentId + '"]').show();
	}
	else
	{
		$('label[for="' + dependentId + '"]').hide();
		$(element).hide();
		parentContainer.hide();
		$('label[for="' + parentId + '"]').hide();
	}
}
