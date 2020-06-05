<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_WINDOWS'), 'tools');
Toolbar::addNew();
Toolbar::deleteList();
Toolbar::spacer();
Toolbar::help('windows');

Html::behavior('tooltip');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_TOOLS_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_NAME', 'toolname', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_TOOLS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_TOOLS_COL_UUID', 'path', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2">In use sessions</th>
				<th scope="col" class="priority-1">Available sessions</th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_TOOLS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		foreach ($this->rows as $row)
		{
			$appinfo = exec("/usr/bin/hz-aws-appstream getapp --appid " . $this->escape(stripslashes($row->get('path'))));
			$appinfoArray = explode("|", $appinfo);
			?>
			<tr>
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-5">
					<?php echo $this->escape($row->get('id')); ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('alias'))); ?>
					</a>
				</td>
				<td class="priority-4">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('title'))); ?>
					</a>
				</td>
				<td class="priority-3">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<?php echo $this->escape(stripslashes($row->get('path'))); ?>
					</a>
				</td>
				<td class="priority-2">
					<?php
					if (count($appinfoArray) > 2)
					{
						echo $appinfoArray[2];
					}
					?>
				</td>
				<td class="priority-1">
					<?php
					if (count($appinfoArray) > 2)
					{
						echo $appinfoArray[3];
					}
					?>
				</td>
				<td>
					<span><?php echo $this->escape($row->get('published')); ?></span>
				</td>
			</tr>
			<?php
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
