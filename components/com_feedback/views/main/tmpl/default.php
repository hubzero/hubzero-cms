<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$jconfig =& JFactory::getConfig();

if ($this->xpoll && $this->wishlist) {
	$numcol = 'four';
} else if (($this->xpoll && !$this->wishlist) || ($this->wishlist && !$this->xpoll)) {
	$numcol = 'three';
} else {
	$numcol = 'two';
}
?>
<div id="content-header" class="full">
	<h2><?php echo $this->title; ?></h2>
</div><!-- / #content-header -->

<div class="main section">
	<p><?php echo JText::sprintf('COM_FEEDBACK_INTRO', $jconfig->getValue('config.sitename')); ?></p>
	
	<div class="<?php echo $numcol; ?> columns first">
		<div class="mainsection" id="story">
			<h3><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=success_story'); ?>"><?php echo JText::_('COM_FEEDBACK_STORY_HEADER'); ?></a></h3>
			<p><?php echo JText::_('COM_FEEDBACK_STORY_OTHER_OPTIONS'); ?></p>
		</div>
	</div>
<?php if ($this->wishlist) { ?>
	<div class="<?php echo $numcol; ?> columns second">
		<div class="mainsection" id="wish">
			<h3><a href="/wishlist/general/1/add"><?php echo JText::_('COM_FEEDBACK_WISHLIST_HEADER'); ?></a></h3>
			<p><?php echo JText::_('COM_FEEDBACK_WISHLIST_DESCRIPTION'); ?></p>
		</div>	
	</div>
<?php } ?>
<?php if ($this->xpoll) { ?>
	<div class="<?php echo $numcol; ?> columns <?php echo ($this->wishlist) ? 'third' : 'second'; ?>">
		<div class="mainsection" id="poll">
			<h3><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=poll'); ?>"><?php echo JText::_('COM_FEEDBACK_POLL_HEADER'); ?></a></h3>
			<p><?php echo JText::_('COM_FEEDBACK_POLL_DESCRIPTION'); ?></p>
		</div>
	</div>
<?php } ?>
	<div class="<?php echo $numcol; ?> columns <?php 
	if ($this->xpoll && $this->wishlist) {
		echo 'forth';
	} else if (($this->xpoll && !$this->wishlist) || ($this->wishlist && !$this->xpoll)) {
		echo 'third';
	} else {
		echo 'second';
	}		
	?>">
		<div class="mainsection" id="problem">
			<h3><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&task=report_problems'); ?>"><?php echo JText::_('COM_FEEDBACK_TROUBLE_HEADER'); ?></a></h3>
			<p><?php echo JText::_('COM_FEEDBACK_TROUBLE_INTRO'); ?></p>
		</div>
	</div>
	<br class="clear" />
</div><!-- / .main section -->

