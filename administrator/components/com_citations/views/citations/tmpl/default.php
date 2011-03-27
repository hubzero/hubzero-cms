<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );
JToolBarHelper::title( JText::_( 'CITATIONS' ), 'addedit.png' );
JToolBarHelper::addNew( 'add', JText::_('NEW') );
JToolBarHelper::editList();
JToolBarHelper::deleteList();

?>
<script type="text/javascript">
function submitbutton(pressbutton) 
{
	var form = $('adminForm');
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
			<input type="text" name="search" id="search" value="<?php echo $this->filters['search']; ?>" />
		</label>

		<label>
			<?php echo JText::_('SORT'); ?>: 
			<select name="sort" id="sort">
				<option value="created DESC"<?php if ($this->filters['sort'] == 'created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('DATE'); ?></option>
				<option value="year"<?php if ($this->filters['sort'] == 'year') { echo ' selected="selected"'; } ?>><?php echo JText::_('YEAR'); ?></option>
				<option value="type"<?php if ($this->filters['sort'] == 'type') { echo ' selected="selected"'; } ?>><?php echo JText::_('TYPE'); ?></option>
				<option value="author ASC"<?php if ($this->filters['sort'] == 'author ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('AUTHORS'); ?></option>
				<option value="title ASC"<?php if ($this->filters['sort'] == 'title ASC') { echo ' selected="selected"'; } ?>><?php echo JText::_('TITLE'); ?></option>
			</select>
		</label>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('TITLE'); ?> / <?php echo JText::_('AUTHORS'); ?></th>
				<th><?php echo JText::_('YEAR'); ?></th>
				<th><?php echo JText::_('TYPE'); ?></th>
				<th><?php echo JText::_('AFFILIATED'); ?></th>
				<th><?php echo JText::_('FUNDED_BY'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$filterstring = ($this->filters['sort']) ? '&amp;sort='.$this->filters['sort'] : '';

for ($i=0, $n=count( $this->rows ); $i < $n; $i++) 
{
	$row =& $this->rows[$i];
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked);" /></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;id[]=<?php echo $row->id; echo $filterstring; ?>"><?php echo $row->title; ?></a><br /><small><?php echo $row->author; ?></small></a></td>
				<td><?php echo $row->year; ?></td>
				<td><?php echo $row->type; ?></td>
				<td><?php if ($row->affiliated == 1) { echo '<span class="check">'.JText::_('YES').'</span>'; } ?></td>
				<td><?php if ($row->fundedby == 1) { echo '<span class="check">'.JText::_('YES').'</span>'; } ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="viewtask" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
