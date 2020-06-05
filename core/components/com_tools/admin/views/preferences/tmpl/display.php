<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu
Toolbar::title(Lang::txt('COM_TOOLS_USER_PREFS'), 'user.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::custom('restoreDefault', 'restore', 'restore', 'COM_TOOLS_USER_PREFS_DEFAULT');

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span5">
				<label for="filter_search_field"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_SEARCH'); ?></label>
				<select name="search_field" id="filter_search_field">
					<option value="username"<?php if ($this->filters['search_field'] == 'username') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_USER_PREFS_USERNAME'); ?></option>
					<option value="name"<?php if ($this->filters['search_field'] == 'name') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_USER_PREFS_NAME'); ?></option>
				</select>

				<label for="filter_search"><?php echo Lang::txt('COM_TOOLS_USER_PREFS_SEARCH_FOR'); ?></label>
				<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_TOOLS_SEARCH_PLACEHOLDER'); ?>" />

				<input type="submit" value="<?php echo Lang::txt('COM_TOOLS_GO'); ?>" />
			</div>
			<div class="col span7">
				<select name="class_alias" id="filter_class_alias" class="filter filter-submit">
					<option value=""<?php if ($this->filters['class_alias'] == '') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_TOOLS_FILTER_SESSION_CLASS'); ?></option>
					<?php foreach ($this->classes as $class) : ?>
						<option value="<?php echo $class->alias; ?>"<?php if ($this->filters['class_alias'] == $class->alias) { echo ' selected="selected"'; } ?>><?php echo $this->escape($class->alias); ?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_USER_PREFS_USER_ID', 'user_id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_USER_PREFS_USERNAME', 'username', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_USER_PREFS_NAME', 'name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_USER_PREFS_CLASS', 'class_alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_USER_PREFS_JOBS', 'jobs', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					echo $this->pagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for ($i=0, $n=count($this->rows); $i < $n; $i++)
		{
			$row = &$this->rows[$i];
			?>
			<tr class="<?php echo "row$k quota-row"; ?>">
				<td>
					<input class="row-id" type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-4">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->user_id); ?>
					</a>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->username); ?>
					</a>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->name); ?>
				</td>
				<td>
					<?php echo ($row->class_alias) ? $this->escape($row->class_alias) : 'custom'; ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape($row->jobs); ?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />
	<?php echo Html::input('token'); ?>
</form>