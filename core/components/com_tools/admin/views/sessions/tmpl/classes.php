<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Menu
Toolbar::title(Lang::txt('COM_TOOLS_SESSION_CLASSES'), 'user.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList('COM_TOOLS_SESSION_CLASSES_CONFIRM_DELETE', 'delete');
Toolbar::spacer();
Toolbar::help('sessionclasses');
?>

<?php
	$this->view('_submenu')
	     ->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" class="checkbox-toggle toggle-all" /></th>
				<th class="priority-3"><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_ID'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_ALIAS'); ?></th>
				<th><?php echo Lang::txt('COM_TOOLS_SESSION_CLASS_JOBS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
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
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" class="checkbox-toggle" />
				</td>
				<td class="priority-3">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->id); ?>
					</a>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape($row->alias); ?>
					</a>
				</td>
				<td>
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
	<input type="hidden" name="task" value="classes" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo Html::input('token'); ?>
</form>