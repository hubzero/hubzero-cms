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

$name = $this->escape(stripslashes($this->instructor->get('name')));
?>
<div class="course-instructor">
	<p class="course-instructor-photo">
		<?php if ($this->instructor->get('public')) { ?>
			<a href="<?php echo JRoute::_($this->instructor->getLink()); ?>">
				<img src="<?php echo $this->instructor->getPicture(); ?>" alt="<?php echo $name; ?>" />
			</a>
		<?php } else { ?>
			<img src="<?php echo $this->instructor->getPicture(); ?>" alt="<?php echo $name; ?>" />
		<?php } ?>
	</p>

	<div class="course-instructor-content cf">
		<h4>
			<?php if ($this->instructor->get('public')) { ?>
				<a href="<?php echo JRoute::_('index.php?option=com_members&id=' . $this->instructor->get('uidNumber')); ?>">
					<?php echo $name; ?>
				</a>
			<?php } else { ?>
				<?php echo $name; ?>
			<?php } ?>
		</h4>
		<p class="course-instructor-org">
			<?php echo $this->escape(stripslashes($this->instructor->get('organization', '--'))); ?>
		</p>
	</div><!-- / .course-instructor-content cf -->

	<?php
	$params = new JRegistry($this->instructor->get('params'));
	if ($params->get('access_bio') == 0 // public
	 || ($params->get('access_bio') == 1 && !JFactory::getUser()->get('guest')) // registered members
	) {
	?>
	<div class="course-instructor-bio">
		<?php if ($this->instructor->get('bio')) { ?>
			<?php echo $this->instructor->getBio('parsed'); ?>
		<?php } else { ?>
			<em><?php echo JText::_('COM_COURSES_INSTRUCTOR_NO_BIO'); ?></em>
		<?php } ?>
	</div>
	<?php } ?>
</div><!-- / .course-instructor -->