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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$base = 'index.php?option=' . $this->option . '&cn=' . $this->group->get('cn') . '&active=' . $this->name;
?>
	<nav>
		<ul class="sub-menu">
			<li<?php if ($this->active == 'collections') { echo ' class="active"'; } ?>>
				<a class="collections count" href="<?php echo JRoute::_($base . '&scope=all'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_COLLECTIONS', $this->collections); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'posts') { echo ' class="active"'; } ?>>
				<a class="posts count" href="<?php echo JRoute::_($base . '&scope=posts'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_POSTS', $this->posts); ?></span>
				</a>
			</li>
			<li<?php if ($this->active == 'followers') { echo ' class="active"'; } ?>>
				<a class="followers count" href="<?php echo JRoute::_($base . '&scope=followers'); ?>">
					<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWERS', $this->followers); ?></span>
				</a>
			</li>
			<?php if ($this->params->get('access-can-follow')) { ?>
				<li<?php if ($this->active == 'following') { echo ' class="active"'; } ?>>
					<a class="following count" href="<?php echo JRoute::_($base . '&scope=following'); ?>">
						<span><?php echo JText::sprintf('PLG_GROUPS_COLLECTIONS_STATS_FOLLOWING', '<strong>' . $this->following . '</strong>'); ?></span>
					</a>
				</li>
			<?php } ?>
		</ul>
	</nav>