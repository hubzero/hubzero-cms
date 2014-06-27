<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
?>

<div id="offering-introduction">
	<div class="instructions">
		<p class="warning"><?php echo JText::_($this->message); ?></p>
	</div>
	<div class="questions">
		<p><strong><?php echo JText::_('How can I enroll?'); ?></strong></p>
		<p><?php echo JText::sprintf('To find out if enrollment is still open and how to enroll, visit the <a href="%s">course overview page</a>', JRoute::_($this->course->link())); ?></p>
		<p><strong><?php echo JText::_('Where can I learn more bout this course?'); ?></strong></p>
		<p><?php echo JText::sprintf('To learn more, either visit the <a href="%s">course overview page</a> or browse the <a href="%s">course listing</a>.', JRoute::_($this->course->link()), JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse')); ?></p>
	</div>
</div>