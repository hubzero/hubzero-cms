<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

JToolBarHelper::title(JText::_('LDAP Configuration'), 'config.png');
JToolBarHelper::preferences($this->option, '550');
?>

<form action="index.php" method="post" name="adminForm" id="item-form">
	<div class="col width-50 fltlft">
		<fieldset class="adminform">
			<legend><?php echo JText::_('HubConfig'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><input type="submit" name="importHubConfig" id="importHubConfig" value="<?php echo JText::_('Import'); ?>" onclick="submitbutton('importHubConfig');" /></td>
						<td><?php echo JText::_('Import old HubConfig LDAP settings.'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="col width-50 fltrt">
		<p class="warning"><strong><?php echo JText::_('Warning!'); ?></strong> <?php echo JText::_('The following operations are irreversible.'); ?></p>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('Users'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><input type="submit" name="exportUsers" id="exportUsers" value="<?php echo JText::_('Export to LDAP'); ?>" onclick="submitbutton('exportUsers');" /></td>
						<td><?php echo JText::_('This will export all relevant user data to the LDAP.'); ?></td>
					</tr>
					<tr>
						<td class="key"><input type="submit" name="deleteUsers" id="deleteUsers" value="<?php echo JText::_('Delete from LDAP'); ?>" onclick="submitbutton('deleteUsers');" /></td>
						<td><?php echo JText::_('This will remove all exported entries in the LDAP.'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
		
		<fieldset class="adminform">
			<legend><?php echo JText::_('Groups'); ?></legend>
			<table class="admintable">
				<tbody>
					<tr>
						<td class="key"><input type="submit" name="exportGroups" id="exportGroups" value="<?php echo JText::_('Export to LDAP'); ?>" onclick="submitbutton('exportGroups');" /></td>
						<td><?php echo JText::_('This will export all relevant group data and memberships to the LDAP.'); ?></td>
					</tr>
					<tr>
						<td class="key"><input type="submit" name="deleteGroups" id="deleteGroups" value="<?php echo JText::_('Delete from LDAP'); ?>" onclick="submitbutton('deleteGroups');" /></td>
						<td><?php echo JText::_('This will remove all exported entries in the LDAP.'); ?></td>
					</tr>
				</tbody>
			</table>
		</fieldset>
	</div>
	<div class="clr"></div>
	
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
</form>