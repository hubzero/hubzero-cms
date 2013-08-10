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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JHTML::_('behavior.tooltip');

// Menu
JToolBarHelper::title(JText::_('BILLBOARDS_MANAGER') . ': <small><small>[ ' . JText::_('BILLBOARDS') . ' ]</small></small>', 'addedit.png');
JToolBarHelper::preferences($this->option, '200', '500');
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList(JText::_('BILLBOARDS_CONFIRM_DELETE'), 'delete');

$juser =& JFactory::getUser();
?>

<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count($this->rows); ?>);" /></th>
				<th><?php echo JText::_('BILLBOARD_ID'); ?></th>
				<th><?php echo JText::_('BILLBOARD_NAME'); ?></th>
				<th><?php echo JText::_('BILLBOARD_COLLECTION_NAME'); ?></th>
				<th style="text-align:center;"><?php echo JText::_('BILLBOARD_ORDERING') . JHTML::_('grid.order', $this->rows); ?></th>
				<th><?php echo JText::_('PUBLISHED'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>

<?php 
	$k = 0;
	for ($i=0, $n=count($this->rows); $i < $n; $i++) 
	{
		$row =& $this->rows[$i];

		// See if the billboard is being edited by someone else
		if ($row->checked_out || $row->checked_out_time != '0000-00-00 00:00:00')
		{
			$checked = JHTMLGrid::_checkedOut($row);
			$info = ($row->checked_out_time != '0000-00-00 00:00:00')
					 ? JText::_('CHECKED_OUT').': '.JHTML::_('date', $row->checked_out_time, $dateFormat, $tz).'<br />'
					 : '';
			if ($row->editor) 
			{
				$info .= JText::_('CHECKED_OUT_BY').': '.$row->editor;
			}
		} 
		else
		{
			$checked = JHTML::_('grid.id', $i, $row->id, false, 'cid');
		}

		$task  = $row->published ? 'unpublish' : 'publish';
		$class = $row->published ? 'published' : 'unpublished';
		$alt   = $row->published ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');
?>

			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $checked; ?></td>
				<td><?php echo $row->id; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;cid[]=<? echo $row->id; ?>" title="Edit this slide"><?php echo $row->name; ?></a></td>
				<td><?php echo $row->bcollection; ?></td>
				<td class="order">
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" class="text_area" style="text-align: center" />
				</td>
				<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task; ?>&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>"><span><?php echo $alt; ?></span></a></td>
			</tr>

<?php $k = 1 - $k; } ?>

		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="billboards" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>
