<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_ZONES'), 'tools');
Toolbar::spacer();
Toolbar::addNew();
ToolBar::makeDefault('default', 'COM_TOOLS_MAKE_DEFAULT');
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('zones');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_ZONE', 'zone', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_TOOLS_COL_TYPE', 'type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_DEFAULT', 'is_default', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_MASTER', 'master', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_TOOLS_COL_SSH_KEY', 'ssh_key_path', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_TOOLS_COL_LOCATIONS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
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
if ($this->rows)
{
	$i = 0;
	foreach ($this->rows as $row)
	{
?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('zone'))); ?></span>
					</a>
				</td>
				<td class="priority-2">
					<?php echo $this->escape(stripslashes($row->get('type'))); ?>
				</td>
				<td>
					<a class="state <?php echo ($row->get('state') == 'up') ? 'publish' : 'unpublish'; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=state&id=' . $row->get('id') . '&state=' . ($row->get('state') == 'up' ? 'down' : 'up') . '&' . Session::getFormToken() . '=1'); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('state'))); ?></span>
					</a>
				</td>
				<td>
					<a class="state <?php echo ($row->get('is_default')) ? 'default' : 'notdefault'; ?>">
						<span><?php echo $this->escape(stripslashes($row->get('is_default'))); ?></span>
					</a>
				</td>
				<td class="priority-4">
					<?php echo $this->escape(stripslashes($row->get('master'))); ?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape(stripslashes($row->get('ssh_key_path'))); ?>
				</td>
				<td class="priority-3">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=locations&zone=' . $row->get('id') . '&tmpl=index'); ?>">
						<span><?php echo $row->locations('count'); ?></span>
					</a>
				</td>
			</tr>
<?php
	}
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
