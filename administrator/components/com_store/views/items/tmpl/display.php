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

$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';

JToolBarHelper::title(JText::_('Store Manager') . $text, 'store.png');
if ($canDo->get('core.admin')) 
{
	JToolBarHelper::preferences($this->option, '550');
}
if ($canDo->get('core.create')) 
{
	JToolBarHelper::addNew();
}
if ($canDo->get('core.edit')) 
{
	JToolBarHelper::editList();
}
if ($canDo->get('core.delete')) 
{
	JToolBarHelper::deleteList();
}
?>
<script type="text/javascript">
public function submitbutton(pressbutton) 
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
		<?php echo count($this->rows); ?> <?php echo JText::_('ITEMS_DISPLAYED'); ?>.

		<label><?php echo JText::_('FILTERBY'); ?>:</label> 
		<select name="filterby" onchange="document.adminForm.submit();">
			<option value="available"<?php if ($this->filters['filterby'] == 'available') { echo ' selected="selected"'; } ?>><?php echo JText::_('INSTORE_ITEMS'); ?></option>
	    	<option value="published"<?php if ($this->filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo JText::_('PUBLISHED'); ?></option>
			<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL_ITEMS'); ?></option>
		</select>

		<label><?php echo JText::_('SORTBY'); ?>:</label> 
		<select name="sortby" onchange="document.adminForm.submit();">
	    	<option value="pricelow"<?php if ($this->filters['sortby'] == 'pricelow') { echo ' selected="selected"'; } ?>><?php echo JText::_('Lowest price'); ?></option>
	    	<option value="pricehigh"<?php if ($this->filters['sortby'] == 'pricehigh') { echo ' selected="selected"'; } ?>><?php echo JText::_('Highlest price'); ?></option>
			<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Date added')); ?></option>
	    	<option value="category"<?php if ($this->filters['sortby'] == 'category') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Category')); ?></option>			
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo strtoupper(JText::_('ID')); ?></th>
				<th scope="col"><?php echo JText::_('CATEGORY'); ?></th>
				<th scope="col"><?php echo JText::_('TITLE'); ?></th>
				<th scope="col"><?php echo JText::_('DESCRIPTION'); ?></th>
				<th scope="col"><?php echo JText::_('PRICE'); ?></th>
				<th scope="col"><?php echo JText::_('TIMES_ORDERED'); ?></th>
				<th scope="col"><?php echo JText::_('INSTOCK'); ?></th>
				<th scope="col"><?php echo JText::_('PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
ximport('Hubzero_View_Helper_Html');
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
			$a_alt = JText::_('TIP_MARK_UNAVAIL');
			$a_img = 'publish_g.png';
			break;
		case '0':
			$a_class = 'unpublish';
			$a_task = 'available';
			$a_alt = JText::_('TIP_MARK_AVAIL');
			$a_img = 'publish_x.png';
			break;
	}
	switch ($row->published)
	{
		case '1':
			$p_class = 'publish';
			$p_task = 'unpublish';
			$p_alt = JText::_('TIP_REMOVE_ITEM');
			$p_img = 'publish_g.png';
			break;
		case '0':
			$p_class = 'unpublish';
			$p_task = 'publish';
			$p_alt = JText::_('TIP_ADD_ITEM');
			$p_img = 'publish_x.png';
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>">
						<?php echo $row->id; ?>
					</a>
				</td>
				<td>
					<?php echo $this->escape(stripslashes($row->category)); ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit')) { ?>
					<a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=edit&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>">
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</a>
<?php } else { ?>
					<span>
						<?php echo $this->escape(stripslashes($row->title)); ?>
					</span>
<?php } ?>
				</td>
				<td>
					<?php echo Hubzero_View_Helper_Html::shortenText(stripslashes($row->description), 300); ?></td>
				<td>
					<?php echo $this->escape(stripslashes($row->price)); ?>
				</td>
				<td>
					<?php echo ($row->allorders) ? $row->allorders : '0'; ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $a_class;?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $a_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $a_alt;?>">
						<span><img src="images/<?php echo $a_img; ?>" width="16" height="16" border="0" alt="<?php echo $a_alt; ?>" /></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $a_class;?>">
						<span><img src="images/<?php echo $a_img; ?>" width="16" height="16" border="0" alt="<?php echo $a_alt; ?>" /></span>
					</span>
<?php } ?>
				</td>
				<td>
<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $p_class;?>" href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=<?php echo $p_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $p_alt;?>">
						<span><img src="images/<?php echo $p_img; ?>" width="16" height="16" border="0" alt="<?php echo $p_alt; ?>" /></span>
					</a>
<?php } else { ?>
					<span class="state <?php echo $p_class;?>">
						<span><img src="images/<?php echo $p_img; ?>" width="16" height="16" border="0" alt="<?php echo $p_alt; ?>" /></span>
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
