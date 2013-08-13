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
?>

<div id="content-header" class="full">
	<h2><?php echo JText::_('PLG_TIME_'.strtoupper($this->active_tab)); ?></h2>
</div>
<div id="time_container">
	<div id="time_sidebar">
		<ul id="time_menu">
			<?php 
				foreach($this->time_plugins as $plugin) 
				{
					if($plugin['return'] == 'html')
					{
						$cls = ($this->active_tab == $plugin['name']) ? 'active' : '';

						$link = JRoute::_('index.php?option='.$this->option.'&active='.$plugin['name']);

						echo "<li><a class=\"{$cls}\" href=\"{$link}\">{$plugin['title']}</a></li>";
					}
				}
			?>
		</ul>
		<div id="quick-links">
			<h4><?php echo JText::_('COM_TIME_QUICK_LINKS'); ?></h4>
			<ul>
				<li>
					<a id="new-record" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=records&action=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_RECORD'); ?>
					</a>
				</li>
				<li>
					<a id="new-task" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=tasks&action=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_TASK'); ?>
					</a>
				</li>
				<li>
					<a id="new-hub" href="<?php echo JRoute::_('index.php?option='.$this->option.'&active=hubs&action=new'); ?>">
						<?php echo JText::_('COM_TIME_NEW_HUB'); ?>
					</a>
				</li>
			</ul>
		</div>
	</div>
	<div id="time_main">
		<div id="time_notifications">
			<?php
				foreach($this->notifications as $notification)
				{
					echo "<p class=\"{$notification['type']}\">{$notification['message']}</p>";
				}
			?>
		</div>
		<div id="time_content" class="time_<?php echo $this->active_tab; ?>">
			<?php
				echo $this->sections[0]['html'];
			?>
		</div>
	</div>
</div>