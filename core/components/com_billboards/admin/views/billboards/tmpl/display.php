<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('tooltip');

// Menu
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS'), 'billboards');
if (User::authorise('core.admin', $this->option))
{
	Toolbar::preferences($this->option);
	Toolbar::spacer();
}
if (User::authorise('core.edit.state', $this->option))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if (User::authorise('core.create', $this->option))
{
	Toolbar::addNew();
}
if (User::authorise('core.edit', $this->option))
{
	Toolbar::editList();
	Toolbar::spacer();
}
if (User::authorise('core.delete', $this->option))
{
	Toolbar::deleteList(Lang::txt('COM_BILLBOARDS_CONFIRM_DELETE'));
}
Toolbar::spacer();
Toolbar::help('billboards');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="toggle" id="checkall-toggle" value="" class="checkbox-toggle toggle-all" />
					<label for="checkall-toggle" class="sr-only visually-hidden"><?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?></label>
				</th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_BILLBOARDS_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_BILLBOARDS_COL_NAME'); ?></th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_BILLBOARDS_COL_COLLECTION'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_BILLBOARDS_COL_ORDERING') . Html::grid('order', $this->rows->toArray()); ?></th>
				<th scope="col" class="priority-1"><?php echo Lang::txt('COM_BILLBOARDS_COL_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
	$i = 0;
	foreach ($this->rows as $row)
	{
		// See if the billboard is being edited by someone else
		if ($row->checked_out || ($row->checked_out_time && $row->checked_out_time != '0000-00-00 00:00:00'))
		{
			$checked = Html::grid('checkedout', $row, User::getInstance($row->checked_out)->get('name'), $row->checked_out_time);
		}
		else
		{
			$checked = Html::grid('id', $i, $row->id, false, 'cid');
		}

		$task  = $row->published ? 'unpublish' : 'publish';
		$class = $row->published ? 'publish' : 'unpublish';
		$alt   = $row->published ? Lang::txt('JPUBLISHED') : Lang::txt('JUNPUBLISHED');
?>
			<tr class="<?php echo "row$i"; ?>">
				<td>
					<?php echo $checked; ?>
				</td>
				<td class="priority-4">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&cid=' . $row->id); ?>"><?php echo $row->name; ?></a>
				</td>
				<td class="priority-2">
					<?php echo $row->collection->name; ?>
				</td>
				<td class="order priority-3">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" />
				</td>
				<td class="priority-1">
					<a class="state <?php echo $class;?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&cid=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_BILLBOARDS_SET_TO', $task); ?>">
						<span><?php echo $alt; ?></span>
					</a>
				</td>
			</tr>
<?php $i++; } ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>