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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->css();
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header>

<div class="group-unapproved">
	<span class="name">
		<?php echo $this->group->get('description'); ?>
	</span>
	<span class="description"><?php echo JText::_('COM_GROUPS_PENDING_APPROVAL_WARNING'); ?></span>

	<?php if (in_array($this->juser->get('id'), $this->group->get('invitees'))) : ?>
		<hr />
		<a href="<?php echo JRoute::_('index.php?option=com_groups&controller=groups&cn='.$this->group->get('cn').'&task=accept'); ?>" class="group-invited">
			<?php echo JText::_('COM_GROUPS_ACCEPT_INVITE'); ?>
		</a>
		<hr />
	<?php endif; ?>

	<a class="all-groups" href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('COM_GROUPS_ALL_GROUPS'); ?></a> | <a class="my-groups" href="<?php echo JRout::_('index.php?option=com_members&task=myaccount&active=groups'); ?>"><?php echo JText::_('COM_GROUPS_MY_GROUPS'); ?></a>
</div>