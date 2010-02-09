<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
</div><!-- / .main section -->
