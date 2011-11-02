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
JToolBarHelper::title( JText::_( 'Citation Types' ), 'addedit.png' );
JToolBarHelper::addNew( 'addtype', JText::_('NEW') );

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
	<table class="adminlist" summary="<?php echo JText::_('TABLE_SUMMARY'); ?>">
		<thead>
			<tr>
				<th><?php echo JText::_('Type ID'); ?></th>
				<th><?php echo JText::_('Type Alias'); ?></th>
				<th><?php echo JText::_('Type Title'); ?></th>
				<th><?php echo JText::_('Manage'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($this->types as $t) : ?>
				<tr>
					<td><?php echo $t['id']; ?></td>
					<td><?php echo $t['type']; ?></td>
					<td><?php echo $t['type_title']; ?></td>
					<td>
						<a href="<?php echo JRoute::_("index.php?option=com_citations&task=edittype&id={$t['id']}"); ?>">Edit</a> | 
						<a href="<?php echo JRoute::_("index.php?option=com_citations&task=deletetype&id={$t['id']}"); ?>">Delete</a>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="viewtask" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
