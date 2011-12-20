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

JToolBarHelper::title( JText::_( 'COM_KB' ), 'addedit.png' );
JToolBarHelper::preferences($this->option, '550');
JToolBarHelper::spacer();
JToolBarHelper::publishList( 'publishc' );
JToolBarHelper::unpublishList( 'unpublishc' );
JToolBarHelper::spacer();
JToolBarHelper::custom( 'newfaq', 'new', '', JText::_('COM_KB_NEW_ARTICLE'), false );
JToolBarHelper::spacer();
JToolBarHelper::addNew( 'newcat', JText::_('COM_KB_NEW_CATEGORY'));
JToolBarHelper::editList();
JToolBarHelper::deleteList( '', 'deletecat', JText::_('COM_KB_DELETE_CATEGORY') );

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
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

<form action="index.php" method="post" name="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('COM_KB_SORT_BY'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="m.title"<?php if ($this->filters['filterby'] == 'm.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_KB_TITLE'); ?></option>
				<option value="m.id"<?php if ($this->filters['filterby'] == 'm.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('COM_KB_ID'); ?></option>
			</select>
		</label> 
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('COM_KB_TITLE'); ?></th>
				<th><?php echo JText::_('COM_KB_PUBLISHED'); ?></th>
				<th><?php echo JText::_('COM_KB_ACCESS'); ?></th>
				<th><?php echo JText::_('COM_KB_SUB_CATEGORIES'); ?></th>
				<th><?php echo JText::_('COM_KB_QUESTIONS'); ?></th>
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row =& $this->rows[$i];
	switch ($row->state)
	{
		case 1:
			$class = 'published';
			$task = 'unpublishc';
			$alt = JText::_('PUBLISHED');
			break;
		case 2:
			$class = 'expired';
			$task = 'publishc';
			$alt = JText::_('TRASHED');
			break;
		case 0:
			$class = 'unpublished';
			$task = 'publishc';
			$alt = JText::_('UNPUBLISHED');
			break;
	}

	if (!$row->access) {
		$color_access = 'style="color: green;"';
		$task_access = 'accessregistered';
	} elseif ($row->access == 1) {
		$color_access = 'style="color: red;"';
		$task_access = 'accessspecial';
	} else {
		$color_access = 'style="color: black;"';
		$task_access = 'accesspublic';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=editcat&amp;id[]=<?php echo $row->id; echo ($this->filters['id']) ? '&amp;cid='.$this->filters['id'] : ''; ?>" title="<?php echo JText::_('COM_KB_EDIT_CATEGORY'); ?>"><?php echo $this->escape(stripslashes($row->title)); ?></a></td>
				<td><a class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task;?>&amp;id[]=<?php echo $row->id; echo ($this->filters['id']) ? '&amp;cid='.$this->filters['id'] : ''; ?>" title="<?php echo JText::sprintf('COM_KB_SET_TASK',$task);?>"><span><?php echo $alt; ?></span></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=<?php echo $task_access; ?>&amp;id=<?php echo $row->id; ?>" <?php echo $color_access; ?> title="<?php echo JText::_('COM_KB_CHANGE_ACCESS'); ?>"><?php echo $row->groupname;?></a></td>
<?php if ($row->cats > 0) { ?>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=categories&amp;id=<? echo $row->id; ?>" title="<?php echo JText::_('COM_KB_VIEW_CATEGORIES_FOR_CATEGORY'); ?>"><?php echo $row->cats; ?></a></td>
<?php } else { ?>
				<td><?php echo $row->cats; ?></td>
<?php } ?>
<?php if ($row->total > 0) { ?>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=category&amp;id=<? echo $row->id; echo ($this->filters['id']) ? '&amp;cid='.$this->filters['id'] : ''; ?>" title="<?php echo JText::_('COM_KB_VIEW_ARTICLES_FOR_CATEGORY'); ?>"><?php echo $row->total.' '.JText::_('COM_KB_ARTICLES'); ?></a></td>
<?php } else { ?>
				<td><?php echo $row->total; ?></td>
<?php } ?>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['id']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

<p><?php echo JText::_('COM_KB_PUBLISH_KEY'); ?></p>
