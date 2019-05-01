<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_EVENTS') . ': ' . Lang::txt('COM_EVENTS_PAGES'), 'event');
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();

?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('COM_EVENTS_SEARCH'); ?>:</label>
		<input type="text" name="search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_EVENTS_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" value="<?php echo Lang::txt('COM_EVENTS_SEARCH_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th colspan="6">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $this->event->id); ?>">
						<?php echo $this->escape(stripslashes($this->event->title)); ?>
					</a>
				</th>
			</tr>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th><?php echo Lang::txt('COM_EVENTS_ID'); ?></th>
				<th><?php echo Lang::txt('COM_EVENTS_TITLE'); ?></th>
				<th colspan="3"><?php echo Lang::txt('COM_EVENTS_REORDER'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					$pageNav = $this->rows->pagination;
					echo $pageNav->render();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$k = 0;
			$i = 0;
			$orderings = $this->rows->fieldsByKey('ordering');
			foreach ($this->rows as $row)
			{
				?>
				<tr class="<?php echo "row$k"; ?>">
					<td><input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" /></td>
					<td><?php echo $row->id; ?></td>
					<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id . '&event_id=' . $this->event->id); ?>"><?php echo $this->escape(stripslashes($row->title)) . ' (' . $this->escape(stripslashes($row->alias)) . ')'; ?></a></td>
					<td>
						<?php echo $pageNav->orderUpIcon($i, ($row->ordering != @$orderings[$i-1])); ?>
					</td>
					<td>
						<?php echo $pageNav->orderDownIcon($i, $pageNav->total, ($row->ordering != @$orderings[$i+1])); ?>
					</td>
					<td><?php echo $row->ordering; ?></td>
				</tr>
				<?php
				$i++;
				$k = 1 - $k;
			}
			?>
		</tbody>
	</table>

	<input type="hidden" name="event_id" value="<?php echo $this->event->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
