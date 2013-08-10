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

$dateFormat = '%m/%d/%Y';
$tz = null;

if (version_compare(JVERSION, '1.6', 'ge'))
{
	$dateFormat = 'm/d/Y';
	$tz = false;
}

$team_ids = array('0' => '');
$class = $this->item->color ? 'pin_'.$this->item->color : 'pin_grey';
	
// Get Comments
$objC = new ProjectComment( $this->database );	
$c = $this->item->activityid ? $objC->getComments( $this->item->id, 'todo' ) : array();

// Is item overdue?
$overdue = '';
if($this->item->duedate && $this->item->duedate != '0000-00-00 00:00:00' && $this->item->duedate <= date( 'Y-m-d H:i:s') ) {
	$overdue = ' ('.JText::_('COM_PROJECTS_OVERDUE').')';
}

// Can it be deleted?
$deletable = ($this->project->role == 1 or $this->item->created_by == $this->uid) ? 1 : 0;

// Get actors' names
$profile =& Hubzero_Factory::getProfile();
$closedby = '';
$author = '';

// Get completer name
if($this->item->closed_by) {
	$profile->load( $this->item->closed_by );	
	$closedby = $profile->get('name');
}

// How long did it take to complete
if($this->item->state == 1) {
	$diff = strtotime($this->item->closed) - strtotime($this->item->created);
	$diff = ProjectsHtml::timeDifference ($diff);
}

// Due?
$due = ($this->item->duedate && $this->item->duedate != '0000-00-00 00:00:00' ) ? JHTML::_('date', $this->item->duedate, $dateFormat, $tz) : '';

// Author name
$profile->load( $this->item->created_by );	
$author = $profile->get('name');

// Use alias or id in urls?
$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;
	
?>
<div id="plg-header">
	<h3 class="todo"><a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=todo'); ?>"><?php echo $this->title; ?></a>
	<?php if($this->item->todolist) { ?> &raquo; <a href="<?php echo JRoute::_('index.php?option='.$this->option.'&id='.$this->project->id.'&active=todo').'/?list='.$this->item->color; ?>"><span class="indlist <?php echo 'pin_'.$this->item->color; ?>"><?php echo $this->item->todolist; ?></span></a> <?php } ?>		
	<?php if($this->item->state == 1) { ?> &raquo; <span class="indlist completedtd"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?state=1'; ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_COMPLETED')); ?></a></span> <?php } ?>
	&raquo; <span class="itemname"><?php echo Hubzero_View_Helper_Html::shortenText($this->item->content, 60, 0); ?></span>
	</h3>
</div>
	<?php
	$index = $this->item->assigned_to ? array_search($this->item->assigned_to, $team_ids) : 0;
	?>
	<div class="<?php echo $this->layout; ?>">
		<div class="columns three first">
			<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>" method="post" id="plg-form" >	
				<div>
				<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
				<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
				<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
				<input type="hidden" name="active" value="todo" />
				<input type="hidden" name="action" value="save" />
				<input type="hidden" name="page" id="tdpage" value="item" />
				<input type="hidden" name="todoid" id="todoid" value="<?php echo $this->item->id; ?>" />
				</div>
			<div id="td-item" class="<?php echo $class; ?>">
				<span class="pin">&nbsp;</span>	
				<div class="todo-content">
					<span class="todo-body"><?php echo stripslashes($this->item->content); ?></span>
					<p class="td-info"><?php echo JText::_('COM_PROJECTS_CREATED').' '.JHTML::_('date', $this->item->created, $dateFormat, $tz).' '.JText::_('COM_PROJECTS_BY').' '.ProjectsHtml::shortenName($author); ?></p>	
					<?php if($this->item->state == 0) { ?>	
					<?php if(count($this->lists) > 0 ) { ?>
						<label><?php echo ucfirst(JText::_('COM_PROJECTS_TODO_CHOOSE_LIST')); ?>:
							<select name="list">
								<option value="" <?php if($this->item->color == '') echo 'selected="selected"'?>><?php echo JText::_('COM_PROJECTS_ADD_TO_NO_LIST'); ?></option>
							<?php foreach($this->lists as $list) { 
							?>
								<option value="<?php echo $list->color; ?>" <?php if($list->color == $this->item->color) echo 'selected="selected"'?>><?php echo stripslashes($list->todolist); ?></option>
							<?php } ?>
							</select>
						</label>
					<?php } ?>
					<label id="td-selector"><?php echo JText::_('COM_PROJECTS_TODO_ASSIGNED_TO'); ?>	
						<select name="assigned">
							<option value=""><?php echo JText::_('COM_PROJECTS_NOONE'); ?></option>
						<?php foreach($this->team as $member) { 
							if($member->userid && $member->userid != 0) { 
								$team_ids[] = $member->userid; ?>
							<option value="<?php echo $member->userid; ?>" class="nameopt" <?php if($member->userid == $this->item->assigned_to) { echo 'selected="selected"'; } ?>><?php echo $member->name; ?></option>
						<?php } } ?>
						</select>
					</label>
					<label><?php echo ucfirst(JText::_('COM_PROJECTS_DUE')); ?>
						<input type="text" name="due" id="dued" class="duebox" value="<?php echo $due; ?>" />
					</label>
					<input type="submit" value="<?php echo JText::_('COM_PROJECTS_SAVE'); ?>" />
					<?php } else if($this->item->state == 1) { ?>
						<p class="td-info"><?php echo JText::_('COM_PROJECTS_TODO_CHECKED_OFF').' '.JHTML::_('date', $this->item->closed, $dateFormat, $tz).' '.JText::_('COM_PROJECTS_BY').' '.ProjectsHtml::shortenName($closedby); ?></p>	
						<p class="td-info"><?php echo JText::_('COM_PROJECTS_TODO_TOOK').' '.$diff.' '.JText::_('COM_PROJECTS_TODO_TO_COMPLETE'); ?></p>	
					<?php } ?>	
				</div>
			</div>
			<?php if($this->item->state == 0) { ?>	
			<p class="todoeditoptions checked"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=changestate').'/?todoid='.$this->item->id.a.'state=1'; ?>"><?php echo JText::_('COM_PROJECTS_TODO_CHECK_OFF'); ?></a> <?php echo JText::_('COM_PROJECTS_THIS_TODO_ITEM'); ?></p>	
			<?php } ?>	
			<?php if($deletable) { ?>
			<p class="todoeditoptions trash"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=delete').'/?todoid='.$this->item->id; ?>" class="confirm-it" id="deltd"><?php echo JText::_('COM_PROJECTS_DELETE'); ?></a> <?php echo JText::_('COM_PROJECTS_THIS_TODO_ITEM'); ?></p>
			<?php } ?>	
			</form>
		</div>
		<div class="columns three second third">
			<h4 class="comment-blurb"><?php echo ucfirst(JText::_('COM_PROJECTS_COMMENTS')).' ('.count($c).')'; ?>:</h4>
			<?php if(count($c) > 0) { ?>
				<ul id="td-comments">
				<?php foreach($c as $comment) { ?>
					<li>
						<p><?php echo $comment->comment; ?></p>
						<p class="todo-assigned"><?php echo $comment->author; ?> <span class="date"> &middot; <?php echo ProjectsHtml::timeAgo($comment->created).' '.JText::_('COM_PROJECTS_AGO'); ?> </span> <?php if($comment->created_by == $this->uid) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=deletecomment').'/?todoid='.$this->item->id.a.'cid='.$comment->id; ?>" id="delc-<?php echo $comment->id; ?>" class="confirm-it">[<?php echo JText::_('COM_PROJECTS_DELETE'); ?>]</a><?php  } ?></p>
					</li>
				<?php } ?>
				</ul>	
			<?php } else { ?>
				<p class="noresults"><?php echo ucfirst(JText::_('COM_PROJECTS_TODO_NO_COMMENTS')); ?></p>
			<?php } ?>
			<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>" method="post" >
				<div class="addcomment td-comment">
					<label><?php echo ucfirst(JText::_('COM_PROJECTS_NEW_COMMENT')); ?>:
						<textarea name="comment" rows="4" cols="50" class="commentarea" id="td-comment"></textarea>
					</label>
						<span class="hint"><?php echo JText::_('COM_PROJECTS_COMMENT_HINT'); ?></span>					
						<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
						<input type="hidden" name="id" value="<?php echo $this->project->id; ?>" />
						<input type="hidden" name="action" value="savecomment" />
						<input type="hidden" name="task" value="view" />
						<input type="hidden" name="active" value="todo" />
						<input type="hidden" name="itemid" value="<?php echo $this->item->id; ?>" />
						<input type="hidden" name="parent_activity" value="<?php echo $this->item->activityid; ?>" />
						<p class="blog-submit"><input type="submit" class="c-submit" id="c-submit" value="<?php echo JText::_('COM_PROJECTS_COMMENT'); ?>" /></p>
				</div>
			</form>
		</div>
		<div class="clear"></div>
	</div>
