/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

//-----------------------------------------------------------
//  Ensure we have our namespace
//-----------------------------------------------------------
if (!HUB) {
	var HUB = {};
}

//----------------------------------------------------------
//  Members scripts
//----------------------------------------------------------
if (!jq) {
	var jq = $;
}
$(function(){
	$('input[data-dependents]').each(function(index, element){
		HUB.Groups.customFields.checkDependentDisplay(element, false);	
		var fieldName = $(element).attr('name');
		var selections = $('[name="' + fieldName + '"]');
		selections.each(function(index, element){
			$(element).on('click', function(e){
				$(selections).each(function(index, element){
					HUB.Groups.customFields.checkDependentDisplay(element, true);
				});
			});
		});

	});

	$('option[data-dependents]').each(function(index, element){
		HUB.Groups.customFields.checkDependentDisplay(element, false);
	});

	$('select').each(function(index, element){
		if ($(element).has('option[data-dependents]'))
		{
			$(element).on('change', function(e){
				var options = $(this).find('option');
				options.each(function(index, element){
					HUB.Groups.customFields.checkDependentDisplay(element, true);
				});
			});
		}
	});
		
});

HUB.Groups['customFields'] = 
	{
		checkDependentDisplay: function(element, recursive, state){
			if (state === undefined || state === true)
			{
				state = $(element).is('input') ? element.checked : element.selected;
			}
			var dependent = HUB.Groups.customFields.getDependentName(element);
			var displayState = state ? "show" : "hide";
			$(dependent).each(function(index, child){
				HUB.Groups.customFields.toggleWithLabel(child, displayState);
				if (recursive)
				{
					HUB.Groups.customFields.checkDependentDisplay(child, recursive, state);
				}
			});
		},
		getDependentName: function(element){
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
		},

		toggleWithLabel: function(element, toggle){
			var dependentId = $(element).attr('id');
			var parentContainer = $(element).closest('.field-wrap');
			var parentId = $(parentContainer).attr('id');
			if (toggle == 'show')
			{
				parentContainer.show();
			}
			else
			{
				parentContainer.hide();
			}
		}
	};
