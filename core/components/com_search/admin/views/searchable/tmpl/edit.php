<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('Solr Search: Edit Facet'));
Toolbar::spacer();

Toolbar::custom('save', 'save', 'save', 'COM_SEARCH_SAVE_FACET', false);
Toolbar::custom('searchindex', 'cancel', 'cancel', 'COM_SEARCH_CANCEL', false);
//Toolbar::cancel();
Toolbar::spacer();

$option = $this->option;

Submenu::addEntry(
	Lang::txt('Overview'),
	'index.php?option='.$option.'&task=configure'
);
Submenu::addEntry(
	Lang::txt('Search Index'),
	'index.php?option='.$option.'&task=searchindex',
	true
);
Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
$this->css('edit')
     ->css('jquery.timepicker.css', 'system')
     ->js('jquery.timepicker', 'system');
?>

<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<!-- Name -->
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SEARCH_FIELD_NAME'); ?>:</label>
						<input type="text" name="fields[title]" id="field-name" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->category->title)); ?>" />
				</div> <!-- /.input-wrap -->
			</fieldset> <!-- /.adminform -->
		</div><!-- /.col span7 -->
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('ID'); ?>:</th>
						<td>
							<?php echo $this->category->get('id', 0); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- /.col span7 -->
	</div><!-- /.grid -->
	<div class="grid">
		<div id="page-1" class="fb-editor col span7">
			<h3>Filters</h3>
			<input type="hidden" name="filter-schema" value="<?php echo $this->escape(json_encode($this->filters)); ?>" />
		</div>
		<div class="col span5">
			<h3>Add Filter</h3>
			<input type="text" value="" name="add-filter" />
			<select name="filter-type">
				<option value="list">List</option>
				<option value="daterange">Date Range</option>
			</select>
			<button id="add-filter">Add Filter</button>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
	<input type="hidden" name="id" value="<?php echo $this->category->id; ?>" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="searchable" />
	<input type="hidden" name="task" value="save" autocomplete="" />
	<input type="hidden" name="action" value="edit" autocomplete="" />
</form>
<script type="text/javascript">
	$(function(){
		$('#add-filter').on('click', function(e){
			e.preventDefault();
			var filterName = $('input[name="add-filter"]').val();
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
			setEditableState($(this));
			$(this).siblings('.editable').each(function(index){
				setReadonlyState($(this));
			});
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
	});
	function renderCurrentFilters(){
		var currentFilters = $('input[name="filter-schema"]').val();
		currentFilters = JSON.parse(currentFilters);
		$.each(currentFilters, function(index, values){
			var newFilter = addNewFilter(index, values);
			$('#page-1').append(newFilter);
		});
	}

	function addNewFilter(filterName, values){
		var filterType = values['type'];
		var filterContainer = $('<article>');
		var headerContainer = $('<header class="editable">');
		var label = filterName;
		headerContainer.append('<h3 class="filter-label col span9">' + label + '</h3>');
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
			minDate.datepicker();
			var maxDateContainer = $('<div class="input-wrap col span6"><label>Maximum Date</label></div>');
			var maxDate = $('<input class="calendar-field" type="text" value="" name="filters[' + filterName + '][params][maxDate]" />');
			if (maxDateValue !== undefined)
			{
				maxDate.val(maxDateValue);
			}
			maxDate.datepicker();
			minDateContainer.append(minDate);	
			maxDateContainer.append(maxDate);
			optionsContainer.append(minDateContainer).append(maxDateContainer);
			return optionsContainer;
		}
	}
</script>
