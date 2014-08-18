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
$due = ($this->item->duedate && $this->item->duedate != '0000-00-00 00:00:00' ) ? JHTML::_('date', strtotime($this->item->duedate), 'm/d/Y') : '';

// Author name
$profile->load( $this->item->created_by );
$author = $profile->get('name');

?>
	<?php
	$index = $this->item->assigned_to ? array_search($this->item->assigned_to, $team_ids) : 0;
	?>
<div id="abox-content">
<h3><?php echo JText::_('PLG_PROJECTS_TODO_EDIT_TODO'); ?></h3>

<div class="<?php echo $this->layout; ?>">
	<form action="<?php echo JRoute::_('index.php?option='.$this->option . '&alias=' . $this->project->alias . '&active=todo'); ?>" method="post" id="plg-form" >
		<fieldset>
			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
			<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
			<input type="hidden" name="active" value="todo" />
			<input type="hidden" name="action" value="save" />
			<input type="hidden" name="page" id="tdpage" value="item" />
			<input type="hidden" name="todoid" id="todoid" value="<?php echo $this->item->id; ?>" />
		</fieldset>
		<section class="section intropage">
			<div id="td-item" class="<?php echo $class; ?>">
				<span class="pin">&nbsp;</span>
				<div class="todo-content">
					<textarea name="content" rows="10" cols="25"><?php echo stripslashes($this->item->content); ?></textarea>
					<div class="todo-edits">
						<?php if (count($this->lists) > 0 ) { ?>
						<label><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_TODO_CHOOSE_LIST')); ?>:
							<select name="list">
								<option value="none" <?php if ($this->item->color == '') echo 'selected="selected"'?>><?php echo JText::_('PLG_PROJECTS_TODO_ADD_TO_NO_LIST'); ?></option>
							<?php foreach ($this->lists as $list) {
							?>
								<option value="<?php echo $list->color; ?>" <?php if ($list->color == $this->item->color) echo 'selected="selected"'?>><?php echo stripslashes($list->todolist); ?></option>
							<?php } ?>
							</select>
						</label>
						<?php } ?>
						<label id="td-selector"><?php echo JText::_('PLG_PROJECTS_TODO_TODO_ASSIGNED_TO'); ?>
							<select name="assigned">
								<option value=""><?php echo JText::_('PLG_PROJECTS_TODO_NOONE'); ?></option>
							<?php foreach ($this->team as $member) {
								if ($member->userid && $member->userid != 0) {
									$team_ids[] = $member->userid; ?>
								<option value="<?php echo $member->userid; ?>" class="nameopt" <?php if ($member->userid == $this->item->assigned_to) { echo 'selected="selected"'; } ?>><?php echo $member->name; ?></option>
							<?php } } ?>
							</select>
						</label>
						<label><?php echo ucfirst(JText::_('PLG_PROJECTS_TODO_DUE')); ?>
							<input type="text" name="due" id="dued" class="duebox" placeholder="mm/dd/yyyy" value="<?php echo $due; ?>" />
						</label>
						<p class="submitarea">
							<input type="submit" value="<?php echo JText::_('PLG_PROJECTS_TODO_SAVE'); ?>" class="btn" />
						</p>
					</div>
				</div>
			</div>
		</section>
	</form>
</div>
</div>