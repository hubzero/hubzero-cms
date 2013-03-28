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
	case 0: $cls = 'first';  break;
	case 1: $cls = 'second'; break;
	case 2: $cls = 'third';  break;
	case 3: $cls = 'fourth'; break;
}

//how many columns are we showing
switch ($this->columns)
{
	case 2: $columns = 'two';   break;
	case 3: $columns = 'three'; break;
	case 4: $columns = 'four';  break;
}

ximport('Hubzero_View_Helper_Html');
?>
<div class="<?php echo $columns; ?> columns <?php echo $cls; ?>">
	<div class="course-list">
		<div class="details">
			<h3>
				<a href="<?php echo JRoute::_('index.php?option=com_courses&controller=course&gid=' . $this->course->get('alias')); ?>">
					<?php echo $this->escape(stripslashes($this->course->get('title'))); ?>
				</a>
			</h3>
		<?php if ($this->course->get('blurb')) { ?>
			<p>
				<?php echo Hubzero_View_Helper_Html::shortenText($this->escape(stripslashes($this->course->get('blurb'))), 150, 0, 0); ?>
			</p>
		<?php } ?>
		<?php if ($this->course->isManager()) { ?>
			<span class="status manager">Manager</span>
		<?php } ?>
		<?php if ($this->course->get('matches')) { ?>
			<ol class="tags">
			<?php foreach ($this->course->get('matches') as $t) { ?>
				<li>
					<a rel="tag" href="<?php echo JRoute::_('index.php?option=com_tags&tag=' . $gt->normalize($t)); ?>"><?php echo $this->escape(stripslashes($t)); ?></a>
				</li>
			<?php } ?>
			</ol>
		<?php } ?>
		</div>
	</div>
</div>