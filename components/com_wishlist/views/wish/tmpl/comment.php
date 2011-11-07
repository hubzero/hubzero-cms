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

// Set the name of the reviewer
$name = JText::_('ANONYMOUS');
if ($this->reply->anonymous != 1) {
	$name = $this->reply->authorname;
}

$commenttype = $this->reply->added_by == $this->wishauthor && $this->reply->anonymous != 1 ?  'submittercomment' : 'plaincomment';
$commenttype = $this->reply->admin && $this->reply->anonymous != 1 ? 'admincomment' : $commenttype;

?>
<dl class="comment-details">
	<dt class="type"><span class="<?php echo $commenttype; ?>"><span><?php echo JText::sprintf('COMMENT'); ?></span></span></dt>
	<dd class="date"><?php echo JHTML::_('date',$this->reply->added, '%d %b, %Y'); ?></dd>
	<dd class="time"><?php echo JHTML::_('date',$this->reply->added, '%I:%M %p'); ?></dd>
</dl>
<div class="comwrap">
	<p class="name"><strong><?php echo $name; ?></strong> <?php echo JText::_('SAID'); ?>:</p>
<?php if ($this->abuse && $this->reply->reports > 0) { ?>
	<p class="warning"><?php echo JText::_('NOTICE_POSTING_REPORTED'); ?></p>
<?php } else { ?>
	<?php if ($this->reply->comment) { ?>
		<p><?php echo stripslashes($this->reply->comment); ?></p>
	<?php } else { ?>
		<p><?php echo JText::_('NO_COMMENT'); ?></p>
	<?php } ?>
	
	<p class="comment-options">
<?php
	// Cannot reply at third level
	if ($this->level < 3) {
		echo '<a ';
		if (!$this->juser->get('guest')) {
			echo 'class="reply" href="javascript:void(0);"';
		} else {
			echo 'class="rep" href="'.JRoute::_('index.php?option='.$this->option.a.'task=reply'.a.'cat=wishcomment'.a.'id='.$this->listid.a.'refid='.$this->reply->id.a.'wishid='.$this->wishid).'" ';
		}
		echo ' id="rep_'.$this->reply->id.'">'.JText::_('REPLY').'</a>';
	}
?>
	<?php if ($this->abuse) { ?>
		<span class="abuse"><a href="<?php echo JRoute::_('index.php?option=com_support'.a.'task=reportabuse'.a.'category=comment'.a.'id='.$this->reply->id.a.'parent='.$this->wishid); ?>"><?php echo JText::_('REPORT_ABUSE'); ?></a></span> 
	<?php } ?>
    <?php if ($this->juser->get('id') == $this->reply->added_by ) { ?>
		<span class="deletewish"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.'task=deletereply'.a.'replyid='.$this->reply->id); ?>"><?php echo JText::_('DELETE_COMMENT'); ?></a></span> 
	<?php } ?>
	</p>
<?php 
	// Add the reply form if needed
	if ($this->level < 3 && !$this->juser->get('guest')) {
		$view = new JView( array('name'=>'wish', 'layout'=>'addcomment') );
		$view->option = $this->option;
		$view->listid = $this->listid;
		$view->level = $this->level;
		$view->row = $this->reply;
		$view->juser = $this->juser;
		$view->wishid = $this->wishid;
		$view->refid = $this->reply->id;
		$view->addcomment = $this->addcomment;
		$view->display();
	}
}
?>
</div>
