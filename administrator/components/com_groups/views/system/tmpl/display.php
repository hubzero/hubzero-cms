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

JToolBarHelper::title(JText::_('COM_GROUPS') . ': <small><small>[ ' . JText::_('System') . ' ]</small></small>', 'groups.png');
//JToolBarHelper::cancel();
?>
<form action="index.php?option=<?php echo $this->option ?>&amp;controller=<?php echo $this->controller; ?>" method="post" name="adminForm" id="adminForm">
	<div class="col width-70 fltlft">
		<table class="adminlist">
			<tbody>
				<tr>
					<th>LDAP Connection:</th>
					<td><span class="state <?php echo $this->status['ldap']; ?>"><span><?php echo $this->status['ldap']; ?></span></span></td>
				</tr>
				<tr>
					<th>LDAP organizationalUnit "ou=groups" exists:</th>
					<td><span class="state <?php echo $this->status['ldap_groupou']; ?>"><span><?php echo $this->status['ldap_groupou']; ?></span></span></td>
				</tr>
				<tr>
					<th>LDAP objectClass "hubGroup" exists:</th>
					<td><span class="state <?php echo $this->status['ldap_hubgroup']; ?>"><span><?php echo $this->status['ldap_hubgroup']; ?></span></span></td>
				</tr>
				<tr>
					<th>LDAP objectClass "posixGroup" exists:</th>
					<td><span class="state <?php echo $this->status['ldap_posixgroup']; ?>"><span><?php echo $this->status['ldap_posixgroup']; ?></span></span></td>
				</tr>
			</tbody>
		</table>
	</div>
	<div class="col width-30 fltlft">
		<p><a class="modal-button" href="index.php?option=com_groups&amp;controller=<?php echo $this->controller; ?>&amp;task=exporttoldap">Export to LDAP</a></p>
		<p><a class="modal-button" href="index.php?option=com_groups&amp;controller=<?php echo $this->controller; ?>&amp;task=importldap">Import from LDAP</a></p>
	</div>
	<div class="clr"></div>
</form>