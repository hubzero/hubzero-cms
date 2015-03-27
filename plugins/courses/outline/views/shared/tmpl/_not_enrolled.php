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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->js('_not_enrolled.js');

$oparams = new JRegistry($this->course->offering()->get('params'));

$price = 'free';
if ($cost = $oparams->get('store_price', false))
{
	$price = 'only $' . $cost;
}
?>

<div id="offering-introduction">
	<div class="instructions">
		<p class="warning"><?php echo JText::_($this->message); ?></p>
	</div>
	<div class="questions">
		<p>
			In order to access this part of the course, you need to enroll.
			If you're enrolled, you're not obligated to complete the course.
			But enrollment lets you:
		</p>
		<ul>
			<li>Take quizzes and exams</li>
			<li>Track your progress</li>
			<li>Add notes to lectures</li>
			<li>Participate in discussions</li>
		</ul>
		<p>
			For more details, check out our 
			<a class="advertise-popup" target="_blank" href="<?php echo JRoute::_('index.php?option=com_help&component=courses&page=basics#why_enroll'); ?>">
				enrollment benefits
			</a>.
		</p>
		<p>
			<strong><?php echo JText::_('I\'m convinced...now what?'); ?></strong>
		</p>
		<p>
			<a href="<?php echo JRoute::_($this->course->offering()->link('enroll')); ?>">Enroll for <?php echo $price; ?>!</a>
		</p>
		<p>
			<strong><?php echo JText::_('Want more details about this and similar courses?'); ?></strong>
			</p>
		<p>
			<?php echo JText::sprintf(
				'To learn more, either visit the <a href="%s">course overview page</a> or browse the <a href="%s">course listing</a>.',
				JRoute::_($this->course->link()),
				JRoute::_('index.php?option=' . $this->option . '&controller=courses&task=browse'));
			?>
		</p>
	</div>
</div>