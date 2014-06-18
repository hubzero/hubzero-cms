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

// No direct access
defined('_JEXEC') or die('Restricted access');
?>

<div class="admin-header">
	<a class="icon-add button push-module" href="<?php echo 'index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&task=push'; ?>">
		<?php echo JText::_('PLG_MEMBERS_DASHBOARD_PUSH_TITLE'); ?>
	</a>
	<a class="icon-add button add-module" href="<?php echo 'index.php?option=com_members&controller=plugins&task=manage&plugin=dashboard&task=add'; ?>">
		<?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADD_MODULES'); ?>
	</a>
	<h3>
		<?php echo JText::_('PLG_MEMBERS_DASHBOARD_MANAGE'); ?>
	</h3>
</div>

<div class="member_dashboard">

	<div class="modules customizable">
		<?php
			foreach ($this->modules as $module)
			{
				// create view object
				$this->view('module', 'display')
				     ->set('admin', $this->admin)
				     ->set('module', $module)
				     ->display();
			}
		?>
	</div>

	<div class="modules-empty">
		<h3><?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_TITLE'); ?></h3>
		<p><?php echo JText::_('PLG_MEMBERS_DASHBOARD_ADMIN_EMPTY_DESC'); ?></p>
	</div>
</div>