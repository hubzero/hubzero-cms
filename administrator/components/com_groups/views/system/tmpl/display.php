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

JToolBarHelper::title(JText::_('GROUPS') . ': <small><small>[ ' . JText::_('System') . ' ]</small></small>', 'user.png');
//JToolBarHelper::cancel();
?>

<dl> 
	<dt>LDAP Connection:</dt>
	<dd><?php echo $this->status['ldap']; ?></dd>
	
	<dt>LDAP organizationalUnit "ou=groups" exists:</dt>
	<dd><?php echo $this->status['ldap_groupou']; ?></dd>
	
	<dt>LDAP objectClass "hubGroup" exists:</dt>
	<dd><?php echo $this->status['ldap_hubgroup']; ?></dd>
	
	<dt>LDAP objectClass "posixGroup" exists:</dt>
	<dd><?php echo $this->status['ldap_posixgroup']; ?></dd>
</dl>

<ul>
	<li><a href="index.php?option=com_groups&amp;controller=<?php echo $this->controller; ?>&amp;task=exporttoldap">Export to LDAP</a></li>
	<li><a href="index.php?option=com_groups&amp;controller=<?php echo $this->controller; ?>&amp;task=importldap">Import from LDAP</a></li>
</ul>
