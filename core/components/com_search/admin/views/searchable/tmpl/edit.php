<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SEARCH_EDIT_COMPONENT'));
Toolbar::spacer();

Toolbar::custom('save', 'save', 'save', 'COM_SEARCH_SAVE_FACET', false);
Toolbar::custom('searchindex', 'cancel', 'cancel', 'JCANCEL', false);
//Toolbar::cancel();
Toolbar::spacer();

$option = $this->option;

$this->view('_submenu', 'shared')
	->display();

$this->css('edit')
	->css('jquery.timepicker.css', 'system')
	->js('jquery.timepicker', 'system')
	->js('editsearchable');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<!-- Name -->
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SEARCH_FIELD_TITLE'); ?>:</label>
					<input type="text" name="fields[title]" id="field-title" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->searchComponent->title)); ?>" />
				</div> <!-- /.input-wrap -->
				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SEARCH_FIELD_CUSTOM'); ?>:</label>
					<input type="text" name="fields[custom]" id="field-custom" size="30" maxlength="250" value="<?php echo $this->escape(stripslashes($this->searchComponent->custom)); ?>" />
				</div> <!-- /.input-wrap -->
			</fieldset> <!-- /.adminform -->
		</div><!-- /.col span7 -->
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th><?php echo Lang::txt('ID'); ?>:</th>
						<td>
							<?php echo $this->searchComponent->get('id', 0); ?>
						</td>
					</tr>
				</tbody>
			</table>
		</div><!-- /.col span7 -->
	</div><!-- /.grid -->
	<div class="grid">
		<div id="page-1" class="fb-editor col span7">
			<h3><?php echo Lang::txt('COM_SEARCH_COMPONENT_FILTERS_LIST');?></h3>
			<input type="hidden" name="filter-schema" value="<?php echo $this->escape(json_encode($this->filters)); ?>" />
			<div class="articles-container">
			</div>
		</div>
		<div class="col span5">
			<h3><?php echo Lang::txt('COM_SEARCH_COMPONENT_FILTER_ADD');?></h3>
			<div class="input-wrap">
				<label for="searchable-filter-field">
					<?php echo Lang::txt('COM_SEARCH_COMPONENT_FILTER_FIELD');?>
				</label>
				<?php if (!empty($this->availableFields)): ?>
					<select name="add-filter" id="searchable-filter-field">
						<?php foreach ($this->availableFields as $field): ?>
						<option value="<?php echo $field;?>"><?php echo $field;?></option>
						<?php endforeach; ?>
					</select>
				<?php else: ?>
					<input type="text" name="add-filter" id="searchable-filter-field" />
				<?php endif; ?>
			</div>
			<div class="input-wrap">
				<label for="filter-type">
					<?php echo Lang::txt('COM_SEARCH_COMPONENT_FILTER_TYPE');?>
				</label>
				<select id="filter-type" name="filter-type">
					<option value="list">List</option>
					<option value="daterange">Date Range</option>
					<option value="textfield">Text</option>
				</select>
			</div>
			<button id="add-filter">Add Filter</button>
		</div>
	</div>
	<?php echo Html::input('token'); ?>
	<input type="hidden" name="id" value="<?php echo $this->searchComponent->id; ?>" />
	<input type="hidden" name="option" value="com_search" />
	<input type="hidden" name="controller" value="searchable" />
	<input type="hidden" name="task" value="save" autocomplete="" />
	<input type="hidden" name="action" value="edit" autocomplete="" />
</form>
