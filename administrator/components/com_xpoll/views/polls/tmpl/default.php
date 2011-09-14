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
JToolBarHelper::title( JText::_( 'XPOLL_MANAGER' ), 'addedit.png' );
JToolBarHelper::publishList();
JToolBarHelper::unpublishList();
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

$juser =& JFactory::getUser();
?>
<form action="index.php" method="post" name="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows ); ?>);" /></th>
				<th><?php echo JText::_('POLL_TITLE'); ?></th>
				<th><?php echo JText::_('OPTIONS'); ?></th>
				<th><?php echo JText::_('PUBLISHED'); ?></th>
				<th><?php echo JText::_('OPEN'); ?></th>
				<th colspan="2"><?php echo JText::_('VOTES'); ?></th>
				<th><?php echo JText::_('CHECKED_OUT'); ?></th>
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row =& $this->rows[$i];

	$task  = $row->published ? 'unpublish' : 'publish';
	$class = $row->published ? 'published' : 'unpublished';
	$alt   = $row->published ? JText::_('PUBLISHED') : JText::_('UNPUBLISHED');

	$task2  = ($row->open == 1) ? 'close' : 'open';
	$class2 = ($row->open == 1) ? 'published' : 'unpublished';
	$alt2   = ($row->open == 1) ? JText::_('OPEN') : JText::_('CLOSED');
?>
			<tr class="<?php echo "row$k"; ?>">
				<?php if ($row->checked_out && $row->checked_out != $juser->get('id')) { ?>
				<td> </td>
				<?php } else { ?>
				<td><input type="checkbox" name="cid[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<?php } ?>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;cid[]=<? echo $row->id; ?>" title="Edit this poll"><?php echo $row->title; ?></a></td>
				<td><?php echo $row->numoptions; ?></td>
				<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task; ?>&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task;?>"><span><?php echo $alt; ?></span></a></td>
				<td><a class="<?php echo $class2;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task2; ?>&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Set this to <?php echo $task2;?>"><span><?php echo $alt2; ?></span></a></td>
				<td><?php echo $row->voters; ?></td>
				<td><?php if ($row->voters > 0) { ?><a class="reset" href="index.php?option=<?php echo $this->option ?>&amp;task=reset&amp;cid[]=<? echo $row->id; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="Reset the stats on this poll"><span>reset</span></a><?php } ?></td>
				<td><?php echo $row->editor; ?></td>
			</tr>
<?php	
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
