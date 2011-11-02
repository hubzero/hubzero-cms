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
JToolBarHelper::title( JText::_( 'Success Story Manager' ), 'addedit.png' );
JToolBarHelper::preferences('com_feedback', '550');
JToolBarHelper::addNew();
JToolBarHelper::editList();
JToolBarHelper::deleteList();

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
			<?php echo JText::_('FEEDBACK_SEARCH'); ?>: 
			<input type="text" name="search" value="<?php echo $this->filters['search']; ?>" />
		</label>
	
		<label>
			<?php echo JText::_('Filter'); ?>: 
			<select name="type" id="type">
				<option value="regular"<?php if (!$this->type || $this->type == 'regular') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SUBMITTED_QUOTES'); ?></option>
				<option value="selected"<?php if ($this->type == 'selected') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SELECTED_QUOTES'); ?></option>
			</select>
		</label>
		
		<label>
			<?php echo JText::_('FEEDBACK_SORT'); ?>: 
			<select name="sortby" id="sortby">
				<option value="date"<?php if ($this->filters['sortby'] == 'date') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_DATE'); ?></option>
				<option value="fullname"<?php if ($this->filters['sortby'] == 'fullname') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_NAME'); ?></option>
				<option value="org"<?php if ($this->filters['sortby'] == 'org') { echo ' selected="selected"'; } ?>><?php echo JText::_('FEEDBACK_SORT_ORGANIZATION'); ?></option>
			</select>
		</label>
		
		<input type="submit" value="<?php echo JText::_('GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>#</th>
				<th><input type="checkbox" name="toggle" value="" onClick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th><?php echo JText::_('FEEDBACK_COL_SUBMITTED'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_AUTHOR'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_ORGANIZATION'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_QUOTE'); ?></th>
				<th><?php echo JText::_('FEEDBACK_COL_PICTURE'); ?></th>
<?php 
if ($this->type == 'regular') {
	echo ('<th>'.JText::_('FEEDBACK_COL_PUBLISH_CONSENT').'</th><th>'.JText::_('FEEDBACK_COL_UID').'</th>');
} else {
	echo ('<th>'.JText::_('FEEDBACK_COL_QUOTES').'</th><th>'.JText::_('FEEDBACK_COL_ROTATION').'</th>');
}
?>   
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="9"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	//cut quote at 100 characters
	$quotepreview = stripslashes($row->quote);
	$quotepreview = substr($quotepreview, 0, 100);
	if (strlen ($quotepreview)>=99) {
		$quotepreview = $quotepreview.'...';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $i; ?></td>
				<td><input type="checkbox" name="id" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onClick="isChecked(this.checked);" /></td>
				<td><?php echo JHTML::_('date', $row->date, '%d %b, %Y'); ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>"><?php echo stripslashes($row->fullname); ?></a></td>
				<td><?php echo ($row->org) ? stripslashes($row->org) : '&nbsp;';?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;task=edit&amp;type=<?php echo $this->type ?>&amp;id=<?php echo $row->id; ?>"><?php echo $quotepreview;?></a></td>
				<td><?php echo ($row->picture != NULL) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '&nbsp;'; ?></td>
				<td><?php if ($this->type == 'regular') {
						echo ($row->publish_ok == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} else {
						echo ($row->notable_quotes == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} ?></td>
				<td><?php if ($this->type == 'regular') {
						echo $row->userid;
					} else {
						echo ($row->flash_rotation == 1 ) ? '<span class="check">'.JText::_('FEEDBACK_YES').'</span>' : '<span class="unpublished"></span>';
					} ?></td>
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
	<input type="hidden" name="type" value="<?php echo $this->type ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
