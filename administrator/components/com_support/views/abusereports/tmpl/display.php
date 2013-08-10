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

$dateFormat = '%d %b, %Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'd M, Y';
	$tz = false;
}

JToolBarHelper::title( JText::_( 'Support' ).': <small><small>[ '.JText::_( 'REPORT_ABUSE' ).' ]</small></small>', 'support.png' );

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

<form action="index.php" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label><?php echo JText::_('SHOW'); ?>:</label> 
		<select name="state" onchange="document.adminForm.submit( );">
			<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo JText::_('OUTSTANDING'); ?></option>
			<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo JText::_('RELEASED'); ?></option>
			<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo JText::_('DELETED'); ?></option>
		</select>

		<label><?php echo JText::_('SORT_BY'); ?>:</label> 
		<select name="sortby" onchange="document.adminForm.submit( );">
			<option value="a.category"<?php if ($this->filters['sortby'] == 'a.category') { echo ' selected="selected"'; } ?>><?php echo JText::_('CATEGORY'); ?></option>
			<option value="a.created DESC"<?php if ($this->filters['sortby'] == 'a.created DESC') { echo ' selected="selected"'; } ?>><?php echo JText::_('MOST_RECENT'); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><?php echo JText::_('STATUS'); ?></th>
				<th><?php echo JText::_('REPORTED_ITEM'); ?></th>
				<th><?php echo JText::_('REASON'); ?></th>
				<th><?php echo JText::_('BY'); ?></th>
				<th><?php echo JText::_('DATE'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php echo $this->pageNav->getListFooter(); ?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$status = '';
	switch ($row->state)
	{
		case '1':
			$status = JText::_('RELEASED');
			break;
		case '0':
			$status = '<span class="yes">'.JText::_('NEW').'</span>';
			break;
	}

	$juser =& JUser::getInstance($row->created_by);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $status;  ?></td>
				<td><a href="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>&amp;task=view&amp;id=<?php echo $row->id; ?>&amp;cat=<?php echo $row->category; ?>"><?php echo ($row->category.' #'.$row->referenceid); ?></a></td>
				<td><?php echo $row->subject; ?></td>
				<td><?php echo $juser->get('username');  ?></td>
				<td><?php echo JHTML::_('date', $row->created, $dateFormat, $tz); ?></td>	   
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="display" />

	<?php echo JHTML::_( 'form.token' ); ?>
</form>
