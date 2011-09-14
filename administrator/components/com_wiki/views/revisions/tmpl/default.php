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
JToolBarHelper::title( '<a href="index.php?option='.$this->option.'">'.JText::_('Wiki').'</a>: '.JText::_('Page Revisions'), 'addedit.png' );
JToolBarHelper::addNew( 'newrevision' );
JToolBarHelper::editList( 'editrevision' );
JToolBarHelper::deleteList( '', 'deleterevision', JText::_('DELETE') );

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
	<table class="adminlist">
		<tbody>
			<tr>
				<th>Title</th>
				<td><?php echo stripslashes($this->page->title); ?></td>
				<th>Scope</th>
				<td><?php echo stripslashes($this->page->scope); ?></td>
			</tr>
			<tr>
				<th>(ID) Pagename</th>
				<td>(<?php echo $this->page->id; ?>) <?php echo stripslashes($this->page->pagename); ?></td>
				<th>Group</th>
				<td><?php echo stripslashes($this->page->group); ?></td>
			</tr>
			<tr>
		</tbody>
	</table>
	
	<fieldset id="filter">
		<label>
			<?php echo JText::_('Search'); ?>:
			<input type="text" name="search" id="search" value="<?php echo htmlentities($this->filters['search']); ?>" />
		</label>
		<label>
			<?php echo JText::_('Sort by'); ?>: 
			<select name="sortby" onchange="document.adminForm.task='revisions';document.adminForm.submit();">
				<option value="created DESC"<?php if ($this->filters['sortby'] == 'created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Created date/time'); ?></option>
				<option value="version DESC, created DESC"<?php if ($this->filters['sortby'] == 'version DESC, created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('Revision #'); ?></option>
				<option value="id ASC"<?php if ($this->filters['sortby'] == 'id ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('ID'); ?></option>
			</select>
		</label>
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
 				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
 				<th><?php echo JText::_('ID'); ?></th>
 				<th><?php echo JText::_('Revision'); ?></th>
 				<th><?php echo JText::_('Edit Summary'); ?></th>
				<th><?php echo JText::_('Approved'); ?></th>
 				<th><?php echo JText::_('Minor edit'); ?></th>
 				<th><?php echo JText::_('Created'); ?></th>
				<th><?php echo JText::_('Created by'); ?></th>
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
	$row = &$this->rows[$i];

	switch ($row->approved)
	{
		case '1':
			$color_access = 'style="color: green;"';
			$class = 'approved';
			$task = '0';
			$alt = JText::_('Approved');
			break;
		case '0':
			$color_access = 'style="color: red;"';
			$class = 'unapprove';
			$task = '1';
			$alt = JText::_('Not approved');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=editrevision&amp;id[]=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Edit revision'); ?>">Revision <?php echo stripslashes($row->version); ?></a></td>
				<td><?php echo stripslashes($row->summary); ?></td>
				<td><a <?php echo $color_access; ?> class="<?php echo $class;?>" href="index.php?option=<?php echo $this->option ?>&amp;task=toggleapprove&amp;id=<?php echo $row->id; ?>&amp;pageid=<?php echo $this->filters['pageid']; ?>&amp;approve=<?php echo $task; ?>&amp;<?php echo JUtility::getToken(); ?>=1" title="<?php echo JText::_('Set state'); ?>"><span><?php echo $alt; ?></span></a></td>
				<td><?php echo $row->minor_edit; ?></td>
				<td><?php echo $row->created; ?></td>
				<td><?php echo $row->created_by_name; ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="pageid" value="<?php echo $this->filters['pageid']; ?>" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
