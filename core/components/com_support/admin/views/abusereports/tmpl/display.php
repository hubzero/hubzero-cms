<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_REPORTS'), 'support.png');

Html::behavior('framework');

$this->view('_submenu')->display();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-state"><?php echo Lang::txt('COM_SUPPORT_SHOW'); ?>:</label>
		<select name="state" id="filter-state" class="filter filter-submit">
			<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_OUTSTANDING'); ?></option>
			<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_RELEASED'); ?></option>
			<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_DELETED'); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_REPORTED_ITEM'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_REASON'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_BY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_DATE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
				// Initiate paging
				echo $this->rows->pagination;
				?></td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$k = 0;
		foreach ($this->rows as $row)
		{
			$status = '';
			switch ($row->state)
			{
				case '1':
					$status = Lang::txt('COM_SUPPORT_REPORT_RELEASED');
					break;
				case '0':
					$status = Lang::txt('COM_SUPPORT_REPORT_NEW');
					break;
			}

			$user = User::getInstance($row->created_by);
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $row->id;  ?></td>
				<td><?php echo $status;  ?></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=view&id=' . $row->id . '&cat=' . $row->category); ?>"><?php echo $row->category . ' #' . $row->referenceid; ?></a></td>
				<td><?php echo $this->escape($row->subject); ?></td>
				<td><?php echo $this->escape($user->get('username')); ?></td>
				<td><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="display" />

	<?php echo Html::input('token'); ?>
</form>
