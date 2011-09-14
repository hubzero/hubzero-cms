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
JToolBarHelper::title( JText::_( 'Wiki' ), 'addedit.png' );
//JToolBarHelper::publishList( 'publishc' );
//JToolBarHelper::unpublishList( 'unpublishc' );
JToolBarHelper::preferences('com_wiki', '550');
JToolBarHelper::spacer();
JToolBarHelper::custom( 'newrevision', 'new', '', JText::_('New Revision'), false );
JToolBarHelper::spacer();
JToolBarHelper::addNew( 'newpage', JText::_('New Page'));
JToolBarHelper::editList( 'editpage' );
JToolBarHelper::deleteList( '', 'deletepage', JText::_('Delete Page') );

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
			<?php echo JText::_('Search'); ?>:
			<input type="text" name="search" id="search" value="<?php echo ($this->filters['search'] == '') ? htmlentities($this->filters['search']) : ''; ?>" />
		</label>
		<label>
			<?php echo JText::_('Sort by'); ?>:
			<select name="filterby" onchange="document.adminForm.submit( );">
				<option value="title"<?php if ($this->filters['sortby'] == 't.title') { echo ' selected="selected"'; } ?>><?php echo JText::_('Title'); ?></option>
				<option value="id"<?php if ($this->filters['sortby'] == 't.id') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
			</select>
		</label> 
		<input type="submit" value="<?php echo JText::_('Go'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('Title'); ?></th>
				<th><?php echo JText::_('Mode'); ?></th>
				<th><?php echo JText::_('Status'); ?></th>
				<th><?php echo JText::_('Group'); ?></th>
				<th><?php echo JText::_('Revisions'); ?></th>
				<th><?php echo JText::_('Hits'); ?></th>
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
	switch ($row->state)
	{
		case 1:
			$color_access = 'style="color: red;"';
			$class = 'locked';
			$task = '0';
			$alt = JText::_('Locked');
			break;
		case 0:
		default:
			$color_access = 'style="color: green;"';
			$class = 'open';
			$task = '1';
			$alt = JText::_('Open');
			break;
	}

	/*if (!$row->access) {
		$color_access = 'style="color: green;"';
		$task_access = 'accessregistered';
	} elseif ($row->access == 1) {
		$color_access = 'style="color: red;"';
		$task_access = 'accessspecial';
	} else {
		$color_access = 'style="color: black;"';
		$task_access = 'accesspublic';
	}*/
	$params = new JParameter( $row->params );
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id; ?></td>
				<td>
					<a href="index.php?option=<?php echo $this->option ?>&amp;task=editpage&amp;id[]=<?php echo $row->id; ?>" title="<?php echo JText::_('Edit Page'); ?>"><?php echo stripslashes($row->title); ?></a>
					<br /><?php if ($row->scope) { ?><span style="color: #999; font-size: 90%"><?php echo stripslashes($row->scope); ?>/</span> &nbsp; <?php } ?><span style="color: #999; font-size: 90%"><?php echo stripslashes($row->pagename); ?></span>
				</td>
				<td><?php echo $params->get( 'mode' ); ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=togglestate&amp;id=<?php echo $row->id; ?>&amp;state=<?php echo $task; ?>&amp;<?php echo JUtility::getToken(); ?>=1" <?php echo $color_access; ?> title="<?php echo JText::_('Change State'); ?>"><?php echo $alt;?></a></td>
				<td><?php echo $row->group; ?></td>
<?php if ($row->revisions > 0) { ?>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=revisions&amp;pageid=<?php echo $row->id; ?>" title="<?php echo JText::_('VIEW_ARTICLES_FOR_CATEGORY'); ?>"><?php echo $row->revisions.' '.JText::_('revisions'); ?></a></td>
<?php } else { ?>
				<td><?php echo $row->revisions; ?></td>
<?php } ?>
				<td><?php echo $row->hits; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
