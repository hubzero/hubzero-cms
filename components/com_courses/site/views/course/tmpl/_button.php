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
defined('_JEXEC') or die( 'Restricted access' );
?>
	<div class="btn-group-wrap">
		<div class="btn-group dropdown">
			<?php if ($this->course->isManager()) { ?>
				<a class="btn" href="<?php echo JRoute::_($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->offering->get('title'))); ?></a>
			<?php } else { ?>
				<a class="btn" href="<?php echo JRoute::_($this->offering->link('enter')); ?>"><?php echo $this->escape(stripslashes($this->section->get('title'))); ?></a>
			<?php } ?>
			<span class="btn dropdown-toggle"></span>
			<ul class="dropdown-menu">
			<?php
			foreach ($this->sections as $key => $section)
			{
				// Skip the first one
				if ($key == 0 && $this->course->isStudent())
				{
					continue;
				}
				// Set the section
				$this->offering->section($section);
				?>
				<li>
					<a href="<?php echo JRoute::_($this->offering->link()); ?>">
						<?php echo $this->escape(stripslashes($section->get('title'))); ?>
					</a>
				</li>
				<?php
			}
			?>
			</ul>
			<div class="clear"></div>
		</div><!-- /btn-group -->
	</div>