<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('role');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_ROLES'), 'resources');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
		<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_RESOURCES_SEARCH_PLACEHOLDER'); ?>" />

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_TITLE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_RESOURCES_COL_TYPES'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->id; ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape($row->alias); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape($row->alias); ?></span>
						</span>
					<?php } ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
							<span><?php echo $this->escape($row->title); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $this->escape($row->title); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php echo $row->types()->total(); ?>
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
	<input type="hidden" name="sort" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="sort_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>