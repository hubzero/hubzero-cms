<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Add the toolbar items
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS_COLLECTIONS'), 'billboards');
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
Toolbar::help('collections');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_BILLBOARDS_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_BILLBOARDS_COL_COLLECTION'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>

		<?php $i = 0; ?>
		<?php foreach ($this->rows as $row) : ?>
			<tr>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" /></td>
				<td class="priority-3"><?php echo $row->id; ?></td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape(stripslashes($row->name)); ?>
					</a>
				</td>
			</tr>
			<?php $i++; ?>
		<?php endforeach; ?>

		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>