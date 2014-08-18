<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
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

$dateFormat = 'M d, Y';
$team_ids = array('0' => '');
$class = $this->item->color ? 'pin_'.$this->item->color : 'pin_grey';

// Get Comments
$objC = new ProjectComment( $this->database );
$c = $this->item->activityid ? $objC->getComments( $this->item->id, 'todo' ) : array();

// Is item overdue?
$overdue = '';
if ($this->item->duedate && $this->item->duedate != '0000-00-00 00:00:00' && $this->item->duedate <= date( 'Y-m-d H:i:s') ) {
	$overdue = ' ('.JText::_('PLG_PROJECTS_TODO_OVERDUE').')';
}

// Can it be deleted?
$deletable = ($this->project->role == 1 or $this->item->created_by == $this->uid) ? 1 : 0;

// Get actors' names
$profile = \Hubzero\User\Profile::getInstance(JFactory::getUser()->get('id'));
$closedby = '';
$author = '';
$assignedto = '';

// Get completer name
if ($this->item->closed_by) {
	$profile->load( $this->item->closed_by );
	$closedby = $profile->get('name');
}
// Get assignee name
if ($this->item->assigned_to) {
	$profile->load( $this->item->assigned_to );
	$assignedto = $profile->get('name');
}
else
{
	$assignedto = JText::_('PLG_PROJECTS_TODO_NOONE');
}

// How long did it take to complete
if ($this->item->state == 1) {
	$diff = strtotime($this->item->closed) - strtotime($this->item->created);
	$diff = ProjectsHtml::timeDifference ($diff);
}

// Due?
$due = ($this->item->duedate && $this->item->duedate != '0000-00-00 00:00:00' ) ? JHTML::_('date', strtotime($this->item->duedate), 'm/d/Y') : JText::_('PLG_PROJECTS_TODO_NEVER');

// Author name
$profile->load( $this->item->created_by );
$author = $profile->get('name');

$goto  = 'alias=' . $this->project->alias;

?>
<div id="plg-header">
	<h3 class="todo"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=todo'); ?>"><?php echo $this->title; ?></a>
	<?php if ($this->item->todolist) { ?> &raquo; <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=todo').'/?list='.$this->item->color; ?>"><span class="indlist <?php echo 'pin_'.$this->item->color; ?>"><?php echo $this->item->todolist; ?></span></a> <?php } ?>
	<?php if ($this->item->state == 1) { ?> &raquo; <span class="indlist completedtd"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?state=1'; ?>"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_COMPLETED')); ?></a></span> <?php } ?>
	&raquo; <span class="itemname"><?php echo \Hubzero\Utility\String::truncate($this->item->content, 60); ?></span>
	</h3>
</div>
	<?php
	$index = $this->item->assigned_to ? array_search($this->item->assigned_to, $team_ids) : 0;
	?>
<div class="<?php echo $this->layout; ?>">
		<section class="section intropage">
			<div class="grid">
				<div class="col span8">
					<div id="td-item" class="<?php echo $class; ?>">
						<span class="pin">&nbsp;</span>
						<div class="todo-content">
							<?php echo stripslashes($this->item->content); ?>
						</div>
					</div>
				</div>
				<div class="col span4 omega td-details">
					<p><?php echo JText::_('PLG_PROJECTS_TODO_CREATED').' '.JHTML::_('date', $this->item->created, $dateFormat).' '.JText::_('PLG_PROJECTS_TODO_BY').' '.ProjectsHtml::shortenName($author); ?></p>
				<?php if ($this->item->state == 0) { ?>
					<p><?php echo JText::_('PLG_PROJECTS_TODO_ASSIGNED_TO') . ' <strong>' . $assignedto . '</strong>'; ?></p>
					<p><?php echo JText::_('PLG_PROJECTS_TODO_DUE') . ': <strong>' . $due . '</strong>'; ?></p>
				<?php } else if ($this->item->state == 1) { ?>
						<p><?php echo JText::_('PLG_PROJECTS_TODO_TODO_CHECKED_OFF').' '.JHTML::_('date', $this->item->closed, $dateFormat).' '.JText::_('PLG_PROJECTS_TODO_BY').' '.ProjectsHtml::shortenName($closedby); ?></p>
						<p><?php echo JText::_('PLG_PROJECTS_TODO_TODO_TOOK').' '.$diff.' '.JText::_('PLG_PROJECTS_TODO_TODO_TO_COMPLETE'); ?></p>
				<?php } ?>
				</div>
			</div>
		</section>
	<p class="td-options">
		<?php if ($this->item->state == 0) { ?>
		<span class="edit"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=edit').'/?todoid='.$this->item->id; ?>" class="showinbox"><?php echo JText::_('PLG_PROJECTS_TODO_EDIT'); ?></a></span>
		<span class="checked"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=changestate').'/?todoid='.$this->item->id.a.'state=1'; ?>"><?php echo JText::_('PLG_PROJECTS_TODO_TODO_CHECK_OFF'); ?></a></span>
		<?php } ?>
		<?php if ($deletable) { ?>
		<span class="trash"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=delete').'/?todoid='.$this->item->id; ?>" class="confirm-it" id="deltd"><?php echo JText::_('PLG_PROJECTS_TODO_DELETE'); ?></a></span>
		<?php } ?>
	</p>
	<div class="comment-wrap">
		<h4 class="comment-blurb"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_COMMENTS')).' ('.count($c).')'; ?>:</h4>
		<?php if (count($c) > 0) { ?>
			<ul id="td-comments">
			<?php foreach ($c as $comment) { ?>
				<li>
					<p><?php echo $comment->comment; ?></p>
					<p class="todo-assigned"><?php echo $comment->author; ?> <span class="date"> &middot; <?php echo ProjectsHtml::timeAgo($comment->created).' '.JText::_('PLG_PROJECTS_TODO_AGO'); ?> </span> <?php if ($comment->created_by == $this->uid) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=deletecomment').'/?todoid='.$this->item->id.a.'cid='.$comment->id; ?>" id="delc-<?php echo $comment->id; ?>" class="confirm-it">[<?php echo JText::_('PLG_PROJECTS_TODO_DELETE'); ?>]</a><?php  } ?></p>
				</li>
			<?php } ?>
			</ul>
		<?php } else { ?>
			<p class="noresults"><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_TODO_NO_COMMENTS')); ?></p>
		<?php } ?>
		<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>" method="post" >
			<div class="addcomment td-comment">
				<label><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_NEW_COMMENT')); ?>:
					<textarea name="comment" rows="4" cols="50" class="commentarea" id="td-comment" placeholder="Write your comment..."></textarea>
				</label>
					<span class="hint"><?php echo JText::_('PLG_PROJECTS_TODO_COMMENT_HINT'); ?></span>
					<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
					<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
					<input type="hidden" name="action" value="savecomment" />
					<input type="hidden" name="task" value="view" />
					<input type="hidden" name="active" value="todo" />
					<input type="hidden" name="itemid" value="<?php echo $this->item->id; ?>" />
					<input type="hidden" name="parent_activity" value="<?php echo $this->item->activityid; ?>" />
					<p class="blog-submit"><input type="submit" class="btn" id="c-submit" value="<?php echo JText::_('PLG_PROJECTS_TODO_ADD_COMMENT'); ?>" /></p>
			</div>
		</form>
	</div>
</div>
