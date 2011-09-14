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
defined('_JEXEC') or die( 'Restricted access' );
$text = (!$this->store_enabled) ? ' <small><small style="color:red;">(store is disabled)</small></small>' : '';

JToolBarHelper::title( JText::_( 'Store Manager' ).$text, 'addedit.png' );
JToolBarHelper::preferences('com_store', '550');

?>
<script type="text/javascript">
public function submitbutton(pressbutton) 
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<p class="extranav"><?php echo JText::_('VIEW'); ?>: <a href="index2.php?option=<?php echo $this->option; ?>&amp;task=orders"><?php echo JText::_('ORDERS'); ?></a> | <strong><?php echo JText::_('STORE'); ?> <?php echo JText::_('ITEMS'); ?></strong></p>

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<?php echo count($this->rows); ?> <?php echo JText::_('ITEMS_DISPLAYED'); ?>.

		<label>
			<?php echo JText::_('FILTERBY'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="available"<?php if ($this->filters['filterby'] == 'available') { echo ' selected="selected"'; } ?>><?php echo JText::_('INSTORE_ITEMS'); ?></option>
	    		<option value="published"<?php if ($this->filters['filterby'] == 'published') { echo ' selected="selected"'; } ?>><?php echo JText::_('PUBLISHED'); ?></option>
				<option value="all"<?php if ($this->filters['filterby'] == 'all') { echo ' selected="selected"'; } ?>><?php echo JText::_('ALL_ITEMS'); ?></option>
			</select>
		</label> 

		<label>
			<?php echo JText::_('SORTBY'); ?>:
			<select name="sortby" onchange="document.adminForm.submit( );">
	    		<option value="pricelow"<?php if ($this->filters['sortby'] == 'pricelow') { echo ' selected="selected"'; } ?>><?php echo JText::_('Lowest price'); ?></option>
	    		<option value="pricehigh"<?php if ($this->filters['sortby'] == 'pricehigh') { echo ' selected="selected"'; } ?>><?php echo JText::_('Highlest price'); ?></option>
				<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Date added')); ?></option>
	    		<option value="category"<?php if ($this->filters['sortby'] == 'category') { echo ' selected="selected"'; } ?>><?php echo ucfirst(JText::_('Category')); ?></option>			
			</select>
		</label> 
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo strtoupper(JText::_('ID')); ?></th>
				<th><?php echo JText::_('CATEGORY'); ?></th>
				<th><?php echo JText::_('TITLE'); ?></th>
				<th><?php echo JText::_('DESCRIPTION'); ?></th>
				<th><?php echo JText::_('PRICE'); ?></th>
				<th><?php echo JText::_('TIMES_ORDERED'); ?></th>
				<th><?php echo JText::_('INSTOCK'); ?></th>
				<th><?php echo JText::_('PUBLISHED'); ?></th>
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$status='';
	switch ($row->available)
	{
		case '1':
			$a_class = 'published';
			$a_task = 'unavail';
			$a_alt = JText::_('TIP_MARK_UNAVAIL');
			break;
		case '0':
			$a_class = 'unpublished';
			$a_task = 'avail';
			$a_alt = JText::_('TIP_MARK_AVAIL');
			break;
	}
	switch ($row->published)
	{
		case '1':
			$p_class = 'published';
			$p_task = 'unpublish';
			$p_alt = JText::_('TIP_REMOVE_ITEM');
			break;
		case '0':
			$p_class = 'unpublished';
			$p_task = 'publish';
			$p_alt = JText::_('TIP_ADD_ITEM');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=storeitem&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>"><?php echo $row->id; ?></a></td>
				<td><?php echo $row->category;  ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=storeitem&amp;id=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ITEM_DETAILS'); ?>"><?php echo stripslashes($row->title); ?></a></td>
				<td><?php echo Hubzero_View_Helper_Html::shortenText($row->description, 300);  ?></td>
				<td><?php echo $row->price ?></td>
				<td><?php echo ($row->allorders) ? $row->allorders : '0';  ?></td>
				<td><a class="<?php echo $a_class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $a_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $a_alt;?>"><span><?php echo $a_alt; ?></span></a></td>
				<td><a class="<?php echo $p_class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $p_task;?>&amp;id=<?php echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo $p_alt;?>"><span><?php echo $p_alt; ?></span></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="storeitems" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
