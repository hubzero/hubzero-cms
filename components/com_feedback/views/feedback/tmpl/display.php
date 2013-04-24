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

$jconfig =& JFactory::getConfig();
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div id="introduction" class="section">
	<div class="aside">
		<h3><?php echo JText::_('Participate in the Community'); ?></h3>
		<ul>
			<li><a href="<?php echo JRoute::_('index.php?option=com_answers'); ?>"><?php echo JText::_('Questions &amp; Answers'); ?></a></li>
			<li><a href="<?php echo JRoute::_('index.php?option=com_forum'); ?>"><?php echo JText::_('Forum'); ?></a></li>
			<li><a href="<?php echo JRoute::_('index.php?option=com_groups'); ?>"><?php echo JText::_('Groups'); ?></a></li>
		</ul>
	</div><!-- / .aside -->
	<div class="subject">
		<h3><?php echo JText::_('Have something to say?'); ?></h3>
		<p><?php echo JText::sprintf('COM_FEEDBACK_INTRO', $jconfig->getValue('config.sitename')); ?></p>
	</div><!-- / .subject -->
	<div class="clear"></div>
</div><!-- / #introduction.section -->

<div class="section">
	<div class="four columns first">
		<h2><?php echo JText::_('Ways to submit feedback'); ?></h2>
	</div><!-- / .four columns first -->
	<div class="four columns second third fourth">
		<div class="two columns first">
			<div class="story">
				<h3><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=success_story'); ?>"><?php echo JText::_('COM_FEEDBACK_STORY_HEADER'); ?></a></h3>
				<p><?php echo JText::_('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
				<p><a class="more btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=success_story'); ?>"><?php echo JText::_('Start writing a story'); ?></a></p>
			</div>
		</div><!-- / .two columns first -->
		<div class="two columns second">
			<div class="report">
				<h3><a href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo JText::_('COM_FEEDBACK_TROUBLE_HEADER'); ?></a></h3>
				<p><?php echo JText::_('COM_FEEDBACK_TROUBLE_INTRO'); ?></p>
				<p><a class="more btn" href="<?php echo JRoute::_('index.php?option=com_support&controller=tickets&task=new'); ?>"><?php echo JText::_('Start writing a report'); ?></a></p>
			</div>
		</div><!-- / .two columns second -->
		<div class="clear"></div>
<?php if ($this->wishlist || $this->xpoll) { ?>
		<div class="two columns first">
<?php if ($this->wishlist) { ?>
			<div class="wish">
				<h3><a href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>"><?php echo JText::_('COM_FEEDBACK_WISHLIST_HEADER'); ?></a></h3>
				<p><?php echo JText::_('COM_FEEDBACK_WISHLIST_DESCRIPTION'); ?></p>
				<p><a class="more btn" href="<?php echo JRoute::_('index.php?option=com_wishlist'); ?>"><?php echo JText::_('View wish list'); ?></a></p>
			</div>
<?php } ?>
		</div><!-- / .two columns first -->
		<div class="two columns second">
<?php if ($this->poll) { ?>
			<div class="poll">
				<h3><a href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=poll'); ?>"><?php echo JText::_('COM_FEEDBACK_POLL_HEADER'); ?></a></h3>
				<p><?php echo JText::_('COM_FEEDBACK_POLL_DESCRIPTION'); ?></p>
				<p><a class="more btn" href="<?php echo JRoute::_('index.php?option=' . $this->option . '&task=poll'); ?>"><?php echo JText::_('View latest poll'); ?></a></p>
			</div>
<?php } ?>
		</div><!-- / .two columns second -->
		<div class="clear"></div>
<?php } ?>
	</div><!-- / .four columns second third fourth -->
	<div class="clear"></div>
</div><!-- / .section -->