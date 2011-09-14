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
JToolBarHelper::title( JText::_( 'GROUPS' ), 'user.png' );
JToolBarHelper::preferences('com_groups', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList('delete','delete');

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = document.getElementById('adminForm');
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter">
		<label>
			<?php echo JText::_('SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
		<label>
			<?php echo JText::_('TYPE'); ?>:
			<select name="type">
				<option value="all"<?php echo ($this->filters['type'][0] == 'all') ? ' selected="selected"' : ''; ?>><?php echo JText::_('ALL'); ?></option>
				<option value="hub"<?php echo ($this->filters['type'][0] == 'hub') ? ' selected="selected"' : ''; ?>>hub</option>
				<option value="system"<?php echo ($this->filters['type'][0] == 'system') ? ' selected="selected"' : ''; ?>>system</option>
				<option value="project"<?php echo ($this->filters['type'][0] == 'project') ? ' selected="selected"' : ''; ?>>project</option>
				<option value="partner"<?php echo ($this->filters['type'][0] == 'partner') ? ' selected="selected"' : ''; ?>>partner</option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>
	
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
		 	<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('ID'); ?></th>
				<th><?php echo JText::_('CN'); ?></th>
				<th><?php echo JText::_('NAME'); ?></th>
				<th><?php echo JText::_('TYPE'); ?></th>
				<th><?php echo JText::_('PUBLISHED'); ?></th>
				<th><?php echo JText::_('APPLICANTS'); ?></th>
				<th><?php echo JText::_('INVITEES'); ?></th>
				<th><?php echo JText::_('MANAGERS'); ?></th>
				<th><?php echo JText::_('TOTAL_MEMBERS'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$group = new Hubzero_Group();
	//$group->gidNumber = $row->gidNumber;
	//$group->cn = $row->cn;
	$group->read($row->gidNumber);

	$applicants = count($group->get('applicants'));
	$invitees   = count($group->get('invitees'));
	$managers   = count($group->get('managers'));
	$members    = count($group->get('members'));

	switch ($row->type)
	{
		case '0': $type = 'system';  break;
		case '1': $type = 'hub';     break;
		case '2': $type = 'project'; break;
		case '3': $type = 'partner'; break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->cn ?>" onclick="isChecked(this.checked);" /></td>
				<td><?php echo $row->gidNumber; ?></td>
				<td><?php echo $row->cn; ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<? echo $row->cn; ?>"><?php echo stripslashes($row->description); ?></a></td>
				<td><?php echo $type; ?></td>
				<td>
					<?php 
						if($row->published) {
							//echo '<span class="check">'.JText::_('YES').'</span>';
							echo ' <a href="index.php?option='.$this->option.'&amp;task=unpublish&amp;id[]='.$row->cn.'" title="Unpublish Group"><img src="/administrator/images/publish_g.png" /></a>';
						} else {
							//echo '<span class="off">'.JText::_('No').'</span>';
							echo ' <a href="index.php?option='.$this->option.'&amp;task=publish&amp;id[]='.$row->cn.'" title="Publish Group"><img src="/administrator/images/publish_x.png" /></a>';
						}
					?>
				</td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $applicants; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $invitees; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $managers; ?></a></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=manage&amp;gid=<? echo $row->cn; ?>"><?php echo $members; ?></a></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
