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

JToolBarHelper::title(JText::_('COM_RESOURCES') . ': ' . JText::_('COM_RESOURCES_ADD_CHILD'), 'addedit.png' );
JToolBarHelper::cancel();

?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<h3><?php echo stripslashes($this->parent->title); ?></h3>

	<fieldset class="adminform">
		<legend><span><?php echo JText::_('COM_RESOURCES_ADD_CHILD_CHOOSE'); ?></span></legend>

		<?php if ($this->getError()) { echo '<p class="error">' . implode('<br />', $this->getErrors()) . '</p>'; } ?>

		<table class="admintable">
			<tbody>
				<tr>
					<td>
						<input type="radio" name="method" id="child_create" value="create" checked="checked" />
						<label for="child_create"><?php echo JText::_('COM_RESOURCES_ADD_CHILD_CREATE'); ?></label>
					</td>
				</tr>
				<tr>
					<td>
						<input type="radio" name="method" id="child_existing" value="existing" />
						<label for="child_existing"><?php echo JText::_('COM_RESOURCES_ADD_CHILD_EXISTING'); ?></label> - <?php echo JText::_('COM_RESOURCES_FIELD_RESOURCE_ID'); ?>: <input type="text" name="childid" id="childid" value="" />
					</td>
				</tr>
				<tr>
					<td><input type="submit" name="Submit" value="<?php echo JText::_('COM_RESOURCES_NEXT'); ?>" /></td>
				</tr>
			</tbody>
		</table>

		<input type="hidden" name="step" value="2" />
		<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
		<input type="hidden" name="pid" value="<?php echo $this->pid; ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

		<?php echo JHTML::_('form.token'); ?>
	</fieldset>
</form>
