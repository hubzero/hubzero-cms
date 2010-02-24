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

// Set the name of the reviewer
$name = JText::_('PLG_RESOURCES_REVIEWS_ANONYMOUS');
if ($this->reply->anonymous != 1) {
	$name = JText::_('PLG_RESOURCES_REVIEWS_UNKNOWN');
	$ruser =& JUser::getInstance($this->reply->added_by);
	if (is_object($ruser)) {
		$name = $ruser->get('name');
	}
}
?>
<dl class="comment-details">
	<dt class="type"><span class="plaincomment"><span><?php echo JText::sprintf('PLG_RESOURCES_REVIEWS_COMMENT'); ?></span></span></dt>
	<dd class="date"><?php echo JHTML::_('date',$this->reply->added, '%d %b, %Y'); ?></dd>
	<dd class="time"><?php echo JHTML::_('date',$this->reply->added, '%I:%M %p'); ?></dd>
</dl>
<div class="cwrap">
	<p class="name"><strong><?php echo $name; ?></strong> <?php echo JText::_('PLG_RESOURCES_REVIEWS_SAID'); ?>:</p>
<?php if ($this->abuse && $this->reply->abuse_reports > 0) { ?>
	<p class="warning"><?php echo JText::_('PLG_RESOURCES_REVIEWS_NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<p><?php echo stripslashes($this->reply->comment); ?></p>
	<?php } else { ?>
		<p><?php echo JText::_('PLG_RESOURCES_REVIEWS_NO_COMMENT'); ?></p>
	<?php } ?>
	
	<p class="comment-options">
<?php
	// Cannot reply at third level
	if ($this->level < 3) {
		echo '<a ';
		if (!$this->juser->get('guest')) {
			echo 'class="showreplyform" href="javascript:void(0);"';
		} else {
			echo 'href="'.JRoute::_('index.php?option='.$this->option.'&task=reply&category=answercomment&id='.$this->id.'&refid='.$this->reply->id).'" ';
		}
		echo '" id="rep_'.$this->reply->id.'">'.JText::_('PLG_RESOURCES_REVIEWS_REPLY').'</a>';
	}
?>
	<?php if ($this->abuse) { ?>
		<span class="abuse"><a href="<?php echo JRoute::_('index.php?option=com_support&task=reportabuse&category=comment&id='.$this->reply->id.'&parent='.$this->id); ?>"><?php echo JText::_('PLG_RESOURCES_REVIEWS_REPORT_ABUSE'); ?></a></span> 
	<?php } ?>
	</p>
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) {
		$view = new Hubzero_Plugin_View(
			array(
				'folder'=>'resources',
				'element'=>'reviews',
				'name'=>'browse',
				'layout'=>'addcomment'
			)
		);
		$view->option = $this->option;
		$view->row = $this->reply;
		$view->juser = $this->juser;
		$view->level = $this->level;
		$view->resource = $this->resource;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div>