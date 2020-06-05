<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('type');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_TYPES'), 'resources');
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
		<label for="category"><?php echo Lang::txt('COM_RESOURCES_FILTER_CATEGORY'); ?>:</label>
		<?php echo \Components\Resources\Helpers\Html::selectType($this->cats, 'category', $this->filters['category'], 'category', Lang::txt('COM_RESOURCES_SELECT'), '', '', ''); ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_TITLE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_CATEGORY', 'category', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col" class="priority-1"><?php echo Html::grid('sort', 'COM_RESOURCES_COL_PUBLISHED', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			if ($row->get('state') == 0)
			{
				$alt  = Lang::txt('JUNPUBLISHED');
				$task = 'publish';
				$cls  = 'unpublish';
			}
			else if ($row->get('state') == 1)
			{
				$alt  = Lang::txt('JPUBLISHED');
				$cls  = 'publish';
				$task = 'unpublish';
			}
			else if ($row->get('state') == 2)
			{
				$alt  = Lang::txt('JTRASHED');
				$task = 'publish';
				$cls  = 'trash';
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php if ($canDo->get('core.edit') && $row->id != 7) { ?>
						<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape(stripslashes($row->get('type'))); ?>
						</a>
					<?php } else { ?>
						<span>
							<?php echo $this->escape(stripslashes($row->get('type'))); ?>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
				</td>
				<td class="priority-2">
					<?php
					$cat_title = '';
					foreach ($this->cats as $cat)
					{
						if ($row->get('category') == $cat->get('id'))
						{
							$cat_title = $cat->get('type');
						}
					}
					echo $this->escape(stripslashes($cat_title));
					?>
				</td>
				<td class="priority-1">
					<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
			$i++;
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
