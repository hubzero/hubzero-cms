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

Toolbar::title(Lang::txt('COM_SEARCH_EDIT_COMPONENT'));
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
	Lang::txt('Searchable Components'),
	'index.php?option='.$option.'&task=display&controller=searchable',
	true
);
Submenu::addEntry(
	Lang::txt('Index Blacklist'),
	'index.php?option='.$option.'&task=manageBlacklist'
);
$this->css('edit')
	->css('jquery.timepicker.css', 'system')
	->js('jquery.timepicker', 'system')
	->js('editsearchable');
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
