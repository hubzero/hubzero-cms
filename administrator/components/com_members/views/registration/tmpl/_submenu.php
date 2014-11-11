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

$controller = JRequest::getCmd('controller', 'registration');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($controller == 'registration') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&controller=registration'); ?>"><?php echo JText::_('COM_MEMBERS_REGISTRATION_CONFIG'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'organizations') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&controller=organizations'); ?>"><?php echo JText::_('COM_MEMBERS_ORGANIZATIONS'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'employers') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&controller=employers'); ?>"><?php echo JText::_('COM_MEMBERS_ORGTYPE'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'incremental') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&controller=incremental'); ?>"><?php echo JText::_('COM_MEMBERS_INCREMENTAL'); ?></a>
		</li>
		<li>
			<a<?php if ($controller == 'premis') { echo ' class="active"'; } ?> href="<?php echo JRoute::_('index.php?option=com_members&controller=premis'); ?>"><?php echo JText::_('COM_MEMBERS_PREMIS'); ?></a>
		</li>
	</ul>
</nav><!-- / .sub-navigation -->