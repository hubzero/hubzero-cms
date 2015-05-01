<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Store\Helpers\Permissions::getActions('component');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

Toolbar::title(Lang::txt('COM_STORE_MANAGER') . $text, 'store.png');
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
		<label for="filter-filterby"><?php echo Lang::txt('COM_STORE_FILTERBY'); ?>:</label>
		<select name="filterby" id="filter-filterby" onchange="document.adminForm.submit();">
			<option value="new"<?php if ($this->filters['filterby'] == 'new') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_NEW'); ?> <?php echo ucfirst(Lang::txt('COM_STORE_ORDERS')); ?></option>
			<option value="processed"<?php if ($this->filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_COMPLETED'); ?> <?php echo ucfirst(Lang::txt('COM_STORE_ORDERS')); ?></option>
			<option value="cancelled"<?php if ($this->filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_CANCELLED'); ?> <?php echo ucfirst(Lang::txt('COM_STORE_ORDERS')); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ALL'); ?> <?php echo ucfirst(Lang::txt('COM_STORE_ORDERS')); ?></option>
		</select>

		<label for="filter-sortby"><?php echo Lang::txt('COM_STORE_SORTBY'); ?>:</label>
		<select name="sortby" id="filter-sortby" onchange="document.adminForm.submit();">
			<option value="m.ordered"<?php if ($this->filters['sortby'] == 'm.ordered') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_ORDER_DATE'); ?></option>
			<option value="m.status_changed"<?php if ($this->filters['sortby'] == 'm.status_changed') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_STORE_LAST_STATUS_CHANGE'); ?></option>
			<option value="m.id DESC"<?php if ($this->filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>><?php echo ucfirst(Lang::txt('COM_STORE_ORDER')).' '.strtoupper(Lang::txt('COM_STORE_ID')); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" class="priority-5"><?php echo strtoupper(Lang::txt('COM_STORE_ID')); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_STORE_STATUS'); ?></th>
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

	$status = '';
	switch ($row->status)
	{
		case '1':
			$status = strtolower(Lang::txt('COM_STORE_COMPLETED'));
		break;
		case '0':
			$status = '<span class="yes">' . strtolower(Lang::txt('COM_STORE_NEW')) . '</span>';
		break;
		case '2':
			$status = '<span style="color:#999;">' . strtolower(Lang::txt('COM_STORE_CANCELLED')) . '</span>';
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
					<?php echo $status; ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape(stripslashes($row->itemtitles)); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->total); ?>
				</td>
				<td class="priority-2">
					<?php echo $this->escape(stripslashes($row->author)); ?>
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
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo JHTML::_('form.token'); ?>
</form>
