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

switch ($this->count)
{
	case 0: $cls = 'span4';  break;
	case 1: $cls = 'span4'; break;
	case 2: $cls = 'span4 omega'; break;
}
?>
<div class="col <?php echo $cls; ?>">
	<div class="course">
		<a href="<?php echo JRoute::_($this->course->link()); ?>">
			<div class="course-details">
				<div class="course-identity">
					<?php if ($logo = $this->course->logo('url')) { ?>
						<img src="<?php echo JRoute::_($logo); ?>" alt="<?php echo $this->escape($this->course->get('title')); ?>" />
					<?php } else { ?>
						<span></span>
					<?php } ?>

					<?php if ($this->course->get('rating', 0) > 4) { ?>
					<div>
						<strong><?php echo JText::_('COM_COURSES_TOP_RATED_COURSE'); ?></strong> <span class="rating">&#x272D;&#x272D;&#x272D;&#x272D;&#x272D;</span>
					</div>
					<?php } else if ($this->course->get('popularity', 0) > 7) { ?>
					<div>
						<strong><?php echo JText::_('COM_COURSES_POPULAR_COURSE'); ?></strong> <span class="popularity">&#xf091;</span>
					</div>
					<?php } ?>
				</div>
				<h3 class="course-title">
					<?php echo $this->escape($this->course->get('title')); ?>
				</h3>
			<?php if ($this->course->get('blurb')) { ?>
				<p class="course-description">
					<?php echo \Hubzero\Utility\String::truncate($this->escape($this->course->get('blurb')), 130); ?>
				</p>
			<?php } ?>
			</div>
		</a>
	</div>
</div>