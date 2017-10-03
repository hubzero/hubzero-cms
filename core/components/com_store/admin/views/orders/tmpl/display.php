<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Store\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('COM_STORE_MANAGER'), 'store');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_store', '550');
}
Toolbar::spacer();
Toolbar::help('orders');

?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-status"><?php echo Lang::txt('COM_STORE_FILTERBY'); ?>:</label>
		<select name="status" id="filter-status" onchange="document.adminForm.submit();">
			<option value="-1"<?php if ($this->filters['status'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ORDERS_ALL'); ?></option>
			<option value="0"<?php if ($this->filters['status'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_NEW'); ?></option>
			<option value="1"<?php if ($this->filters['status'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_COMPLETED'); ?></option>
			<option value="2"<?php if ($this->filters['status'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_CANCELLED'); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" class="priority-5"><?php echo Html::grid('sort', 'COM_STORE_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_STORE_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_STORE_ORDERED_ITEMS'); ?></th>
				<th scope="col" class="priority-4"><?php echo Lang::txt('COM_STORE_TOTAL'); ?> (<?php echo Lang::txt('COM_STORE_POINTS'); ?>)</th>
				<th scope="col" class="priority-2"><?php echo Lang::txt('COM_STORE_BY'); ?></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_STORE_DATE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		$i = 0;
		foreach ($this->rows as $row)
		{
			$status = '';
			$class  = 'completed-item';
			switch ($row->status)
			{
				case '1':
					$status = strtolower(Lang::txt('COM_STORE_COMPLETED'));
				break;
				case '0':
				default:
					$status = strtolower(Lang::txt('COM_STORE_NEW'));
					$class  = 'new-item';
				break;
				case '2':
					$status = strtolower(Lang::txt('COM_STORE_CANCELLED'));
					$class  = 'cancelled-item';
				break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="priority-5">
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=order&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_STORE_VIEW_ORDER'); ?>">
						<?php echo $row->id; ?>
					</a>
				</td>
				<td>
					<span class="<?php echo $class; ?>"><?php echo $status; ?></span>
				</td>
				<td class="priority-4">
					<?php echo $this->escape(stripslashes($row->itemtitles)); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->total); ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape(stripslashes($row->user->get('name'))); ?>
				</td>
				<td class="priority-3">
					<time datetime="<?php echo $row->ordered; ?>"><?php echo Date::of($row->ordered)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=order&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_STORE_VIEW_ORDER'); ?>">
						<?php echo Lang::txt('COM_STORE_DETAILS'); ?>
					</a>
					<?php if ($row->status!=2) { echo '&nbsp;&nbsp;|&nbsp;&nbsp; <a href="' . Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=receipt&id=' . $row->id) . '">' . Lang::txt('COM_STORE_RECEIPT') . '</a>'; } ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>
