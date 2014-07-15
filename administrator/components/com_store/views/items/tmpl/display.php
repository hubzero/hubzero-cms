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

$canDo = StoreHelper::getActions('item');

$text = (!$this->store_enabled) ? ' (store is disabled)' : '';

JToolBarHelper::title(JText::_('COM_STORE_MANAGER') . $text, 'store.png');
if ($canDo->get('core.admin'))
{
	JToolBarHelper::preferences($this->option, '550');
}
if ($canDo->get('core.create'))
{
	JToolBarHelper::addNew();
}
JToolBarHelper::spacer();
JToolBarHelper::help('items');

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
			<option value="available"<?php if ($this->filters['filterby'] == 'available') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_INSTORE_ITEMS'); ?></option>
			<option value="published"<?php if ($this->filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_PUBLISHED'); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_ALL_ITEMS'); ?></option>
		</select>

		<label for="filter-sortby"><?php echo JText::_('COM_STORE_SORTBY'); ?>:</label>
		<select name="sortby" id="filter-sortby" onchange="document.adminForm.submit();">
			<option value="pricelow"<?php if ($this->filters['sortby'] == 'pricelow') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_LOWEST_PRICE'); ?></option>
			<option value="pricehigh"<?php if ($this->filters['sortby'] == 'pricehigh') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_STORE_HIGHEST_PRICE'); ?></option>
			<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('COM_STORE_DATE_ADDED')); ?></option>
			<option value="category"<?php if ($this->filters['sortby'] == 'category') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('COM_STORE_CATEGORY')); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo strtoupper(JText::_('COM_STORE_ID')); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_CATEGORY'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_TITLE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_DESCRIPTION'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_PRICE'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_TIMES_ORDERED'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_INSTOCK'); ?></th>
				<th scope="col"><?php echo JText::_('COM_STORE_PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$status = '';
	switch ($row->available)
	{
		case '1':
			$a_class = 'publish';
			$a_task = 'unavailable';
			$a_alt = JText::_('COM_STORE_TIP_MARK_UNAVAIL');
			break;
		case '0':
			$a_class = 'unpublish';
			$a_task = 'available';
			$a_alt = JText::_('COM_STORE_TIP_MARK_AVAIL');
			break;
	}
	switch ($row->published)
	{
		case '1':
			$p_class = 'publish';
			$p_task = 'unpublish';
			$p_alt = JText::_('COM_STORE_TIP_REMOVE_ITEM');
			break;
		case '0':
			$p_class = 'unpublish';
			$p_task = 'publish';
			$p_alt = JText::_('COM_STORE_TIP_ADD_ITEM');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_STORE_VIEW_ITEM_DETAILS'); ?>">
						<?php echo $row->id; ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->category)); ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('COM_STORE_VIEW_ITEM_DETAILS'); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
				<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
				<?php } ?>
				</td>
				<td>
					<?php echo \Hubzero\Utility\String::truncate(stripslashes($row->description), 300); ?></td>
				<td>
					<?php echo $this->escape(stripslashes($row->price)); ?>
				</td>
				<td>
					<?php echo ($row->allorders) ? $row->allorders : '0'; ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $a_class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $a_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $a_alt;?>">
						<span><?php echo $a_alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $a_class; ?>">
						<span><?php echo $a_alt; ?></span>
					</span>
				<?php } ?>
				</td>
				<td>
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $p_class; ?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $p_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $p_alt;?>">
						<span></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $p_class; ?>">
						<span><?php echo $p_alt; ?></span>
					</span>
				<?php } ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />

	<?php echo JHTML::_('form.token'); ?>
</form>
