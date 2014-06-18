<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = StoreHelper::getActions('component');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

JToolBarHelper::title(JText::_('COM_STORE_MANAGER') . $text, 'store.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences('com_store', '550');
}
JToolBarHelper::spacer();
JToolBarHelper::help('orders');

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
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-filterby"><?php echo JText::_('COM_STORE_FILTERBY'); ?>:</label>
		<select name="filterby" id="filter-filterby" onchange="document.adminForm.submit();">
			<option value="new"<?php if ($this->filters['filterby'] == 'new') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_NEW'); ?> <?php echo ucfirst(JText::_('COM_STORE_ORDERS')); ?></option>
			<option value="processed"<?php if ($this->filters['filterby'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_COMPLETED'); ?> <?php echo ucfirst(JText::_('COM_STORE_ORDERS')); ?></option>
			<option value="cancelled"<?php if ($this->filters['filterby'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_CANCELLED'); ?> <?php echo ucfirst(JText::_('COM_STORE_ORDERS')); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_ALL'); ?> <?php echo ucfirst(JText::_('COM_STORE_ORDERS')); ?></option>
		</select>

		<label for="filter-sortby"><?php echo JText::_('COM_STORE_SORTBY'); ?>:</label>
		<select name="sortby" id="filter-sortby" onchange="document.adminForm.submit();">
			<option value="m.ordered"<?php if ($this->filters['sortby'] == 'm.ordered') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_ORDER_DATE'); ?></option>
			<option value="m.status_changed"<?php if ($this->filters['sortby'] == 'm.status_changed') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_LAST_STATUS_CHANGE'); ?></option>
			<option value="m.id DESC"<?php if ($this->filters['sortby'] == 'm.id DESC') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('COM_STORE_ORDER')).' '.strtoupper(JText::_('COM_STORE_ID')); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo strtoupper(JText::_('COM_STORE_ID')); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_STATUS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_ORDERED_ITEMS'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_TOTAL'); ?> (<?php echo JText::_('COM_STORE_POINTS'); ?>)</th>
				<th scope="col"><?php echo JText::_('COM_STORE_BY'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_DATE'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="7">
					<?php echo $this->pageNav->getListFooter(); ?>
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
			$status = strtolower(JText::_('COM_STORE_COMPLETED'));
		break;
		case '0':
			$status = '<span class="yes">' . strtolower(JText::_('COM_STORE_NEW')) . '</span>';
		break;
		case '2':
			$status = '<span style="color:#999;">' . strtolower(JText::_('COM_STORE_CANCELLED')) . '</span>';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_STORE_VIEW_ORDER'); ?>">
						<?php echo $row->id; ?>
					</a>
				</td>
				<td>
					<?php echo $status; ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->itemtitles)); ?>
				</td>
				<td>
					<?php echo $this->escape($row->total); ?>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->author)); ?>
				</td>
				<td>
					<time datetime="<?php echo $row->ordered; ?>"><?php echo JHTML::_('date', $row->ordered, JText::_('DATE_FORMAT_HZ1')); ?></time>
				</td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=order&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_STORE_VIEW_ORDER'); ?>">
						<?php echo JText::_('COM_STORE_DETAILS'); ?>
					</a>
					<?php if ($row->status!=2) { echo '&nbsp;&nbsp;|&nbsp;&nbsp; <a href="index.php?option=' . $this->option . '&amp;controller=' . $this->controller . '&amp;task=receipt&amp;id=' . $row->id . '">' . JText::_('COM_STORE_RECEIPT') . '</a>'; } ?>
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
