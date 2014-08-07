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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

if (!isset($this->permissions))
{
	$this->permissions = new TimeModelPermissions($this->option);
}

?>

<div class="com_time_navigation">
	<ul class="com_time_menu">
		<?php
			// Get primary controllers
			$controllers = scandir(JPATH_COMPONENT . DS . 'controllers');

			foreach (array('overview', 'records', 'tasks', 'hubs', 'reports') as $tab)
			{
				if (!$this->permissions->can('view.' . $tab))
				{
					continue;
				}
				$cls  = ($this->controller == $tab) ? ' active' : '';
				$link = JRoute::_('index.php?option=' . $this->option . '&controller=' . $tab);

				echo "<li class=\"{$tab}{$cls}\"><a href=\"{$link}\">" . ucfirst($tab) . "</a></li>";
			}
		?>
	</ul>
	<div class="com_time_quick_links">
		<ul>
			<?php if ($this->permissions->can('new.records')) : ?>
				<li>
					<a class="new-record" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=records&task=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_RECORD'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->permissions->can('new.tasks')) : ?>
				<li>
					<a class="new-task" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=tasks&task=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_TASK'); ?>
					</a>
				</li>
			<?php endif; ?>
			<?php if ($this->permissions->can('new.hubs')) : ?>
				<li>
					<a class="new-hub" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&controller=hubs&task=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_HUB'); ?>
					</a>
				</li>
			<?php endif; ?>
		</ul>
	</div>
</div>