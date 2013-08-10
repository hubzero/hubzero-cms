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

$next = $this->filters['start'] + $this->filters['limit'];
$prev = $this->filters['start'] - $this->filters['limit'];
$prev = $prev < 0 ? 0 : $prev;
$whatsleft = $this->total - $this->filters['start'] - $this->filters['limit'];
$team_ids = array('0' => '');
$which = $this->filters['state'] == 1 ? strtolower(JText::_('COM_PROJECTS_COMPLETED')) : JText::_('COM_PROJECTS_OUTSTANDING');
$where = $this->listname ? ' '.JText::_('COM_PROJECTS_TODO_ON_THIS_LIST') : '';
$where.= $this->filters['mine'] == 1 ? ' '.JText::_('COM_PROJECTS_IN_MY_TODOS') : '';

$use_alias = $this->config->get('use_alias', 0);
$goto  = $use_alias ? 'alias='.$this->project->alias : 'id='.$this->project->id;	
?>
<form action="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>" method="post" id="plg-form" >
  <div id="plg-header">	
	<h3 class="todo"><?php if($this->listname or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?> <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>"> <?php } ?><?php echo $this->title; ?><?php if($this->listname or $this->filters['assignedto'] or $this->filters['state'] == 1) { ?></a><?php } ?>
	<?php if($this->listname) { ?> &raquo; <a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?list='.$this->filters['todolist']; ?>"><span class="indlist <?php echo 'pin_'.$this->filters['todolist'] ?>"><?php echo $this->listname; ?></span></a> <?php } ?>
	<?php if($this->filters['assignedto']) { ?> &raquo; <span class="indlist mytodo"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?mine=1'; ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_MY_TODOS')); ?></a></span> <?php } ?>	
	<?php if($this->filters['state']) { ?> &raquo; <span class="indlist completedtd"><?php echo ucfirst(JText::_('COM_PROJECTS_COMPLETED')); ?></span> <?php } ?>	
	</h3>
  </div>
<div>
<?php if($this->filters['state'] != 1 ) { ?>
	<div class="aside">
		<div class="sidebox">
			<h4><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'); ?>"><?php echo ucfirst(JText::_('COM_PROJECTS_TODO_LISTS')); ?></a></h4>
			<ul class="tdlists">
				<?php if (count($this->lists) > 0) {  ?>					
						<?php foreach($this->lists as $list) { 
							$class = $list->color ? 'pin_'.$list->color : 'pin_grey';
							$selected = $list->color == $this->filters['todolist'] ? ' activelist' : '';
						?>
							<li class="<?php echo $class.$selected; ?>">
								<a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?list='.$list->color; ?>"><?php echo stripslashes($list->todolist); ?></a>
							<?php if($selected) { ?>
								<span class="listoptions"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?action=delete'.a.'dl='.$list->color; ?>" id="del-<?php echo $list->color; ?>" class="dellist">[<?php echo JText::_('COM_PROJECTS_DELETE_TODO_LIST'); ?>]</a></span>
								<div class="confirmaction" id="confirm-<?php echo $list->color; ?>"><?php echo JText::_('COM_PROJECTS_TODO_DELETE_ARE_YOU_SURE'); ?>
									<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?action=delete'.a.'dl='.$list->color.a.'all=1'; ?>">&middot; <?php echo JText::_('COM_PROJECTS_TODO_DELETE_ALL_ITEMS'); ?></a></p>
									<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?action=delete'.a.'dl='.$list->color; ?>">&middot; <?php echo JText::_('COM_PROJECTS_TODO_DELETE_LEAVE_ITEMS'); ?></a></p>
									<p><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?list='.$this->filters['todolist']; ?>" id="cnl-<?php echo $list->color; ?>">&middot; <?php echo JText::_('COM_PROJECTS_CANCEL'); ?></a></p>
								</div>
							<?php } ?>
							</li>
						<?php } ?>
				<?php } ?>
				<?php if(!empty($this->unused)) { // can add a list 
					$newcolor = $this->unused[0];
				?>
					<li class="newcolor pin_<?php echo $newcolor; ?>">
						<span class="pin">&nbsp;</span>
						<input type="hidden" name="newcolor" value="<?php echo $newcolor; ?>" />
						<input type="text" name="newlist" value="" maxlength="50" />
						<input type="submit" value="<?php echo JText::_('COM_PROJECTS_ADD'); ?>" class="todo-submit" />	
					</li>
				<?php }  ?>
			</ul>
		</div>
		<?php if($this->filters['mine'] != 1 && $this->filters['state'] != 1) { ?>
			<div class="dragbox droptarget" id="todo-mine">
				<h4><?php echo ucfirst(JText::_('COM_PROJECTS_MY_TODOS')); ?> - <?php echo $this->mine ? '<a href="'.JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?mine=1'.a.'list='.$this->filters['todolist'].'"><strong>'.$this->mine.'</strong> '.JText::_('COM_PROJECTS_ITEMS').'</a> ' : ucfirst(JText::_('COM_PROJECTS_NONE')); ?></h4>
				<div class="js">
					<p><span class="block faded mini"><?php echo JText::_('COM_PROJECTS_TODO_INSTR_DROP'); ?></span></p>
				</div>
			</div>
		<?php } ?>
		<?php if($this->filters['state'] != 1 ) { ?>
			<div class="dragbox droptarget" id="todo-completed">
				<h4><?php echo ucfirst(JText::_('COM_PROJECTS_COMPLETED')); ?> - <?php echo $this->completed ? '<a href="'.JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?state=1'.a.'list='.$this->filters['todolist'].a.'mine='.$this->filters['mine'].'"><strong>'.$this->completed.'</strong> '.JText::_('COM_PROJECTS_ITEMS').'</a> ' : ucfirst(JText::_('COM_PROJECTS_NONE')); ?></h4>
				<div class="js">
					<p><span class="block faded mini"><?php echo JText::_('COM_PROJECTS_TODO_INSTR_DROP'); ?></span></p>
				</div>
			</div>
		<?php } ?>
	</div>
	<div class="subject">
	<?php } ?>
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="id" id="pid" value="<?php echo $this->project->id; ?>" />
		<input type="hidden" name="alias" value="<?php echo $this->project->alias; ?>" />
		<input type="hidden" name="uid" id="uid" value="<?php echo $this->uid; ?>" />
		<input type="hidden" name="active" value="todo" />
		<input type="hidden" name="action" value="save" />
		<input type="hidden" name="todoid" id="todoid" value="0" />
		<input type="hidden" name="page" id="tdpage" value="" />
		<input type="hidden" name="list" id="list" value="<?php echo $this->filters['todolist']; ?>" />
		<input type="hidden" name="state" id="tdstate" value="<?php echo $this->filters['state']; ?>" />
		<input type="hidden" name="mine" value="<?php echo $this->filters['mine']; ?>" />
		<input type="hidden" name="sortby" value="<?php echo $this->filters['sortby']; ?>" />

		<?php if($this->filters['state'] != 1 ) { ?>
	 <div id="newtodo">
		<fieldset>
			<?php if(!$this->filters['todolist'] && count($this->lists) > 0 ) { ?>
				<div id="pinselector"><span class="pin_grey" id="pinner"><span class="show-options">&nbsp;</span></span></div>
				<div id="pinoptions">
					<ul>
						<li>
							<label>
								<input type="radio" name="listt"  class="listclicker" value="" checked="checked" /> <span><?php echo JText::_('COM_PROJECTS_ADD_TO_NO_LIST'); ?></span>
							</label>
						</li>
						<?php foreach($this->lists as $list) { 
							$class = $list->color ? 'pin_'.$list->color : 'pin_grey';
						?>
							<li>
								<label>
									<input type="radio" name="listt" class="listclicker" value="<?php echo $list->color; ?>" /> <span class="<?php echo $class; ?>"><?php echo stripslashes($list->todolist); ?></span>
								</label>
							</li>
						<?php } ?>
					</ul>
				</div>
			<?php } else {  $class = $this->filters['todolist'] ? 'pin_'.$this->filters['todolist'] : 'pin_grey'; ?>
				<span class="showpin"><span class="<?php echo $class; ?>">&nbsp;</span></span>	
			<?php } ?>
			<input name="content" type="text" id="todo-content" maxlength="150" value="" />
			<input type="submit" value="<?php echo JText::_('COM_PROJECTS_ADD'); ?>" class="todo-submit" />	
				<label class="hidden" id="td-selector">
					<?php echo JText::_('COM_PROJECTS_ASSIGNED_TO'); ?>
				<select name="assigned">
					<option value=""><?php echo JText::_('COM_PROJECTS_NOONE'); ?></option>
				<?php foreach($this->team as $member) { if($member->userid && $member->userid != 0) { $team_ids[] = $member->userid; ?>
					<option value="<?php echo $member->userid; ?>" class="nameopt"><?php echo $member->name; ?></option><?php } } ?>
				</select>
				</label>	
		</fieldset>
	</div>
		<?php } ?>
	<p class="todo-nav"><?php echo JText::_('COM_PROJECTS_SHOWING').' '; echo count($this->todos) >= $this->total ? JText::_('COM_PROJECTS_ALL') : '';  echo ' <strong>'.count($this->todos).'</strong>'; echo count($this->todos) >= $this->total ? ' ' : ' '. JText::_('COM_PROJECTS_OUT_OF').' <strong>'.$this->total.'</strong> '; echo $which.' '.JText::_('COM_PROJECTS_TODO_ITEMS').$where.'.';  ?>
		<?php if(count($this->todos) < $this->total) { ?>
		<span class="td-mv"><?php if($this->filters['start'] > 0) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?list='.$this->filters['todolist'].a.'limitstart='.$prev; ?>"><?php } ?>&laquo; <?php echo ucfirst(JText::_('COM_PROJECTS_PREVIOUS')); ?><?php if($this->filters['start'] > 0) { ?></a><?php } ?></span>
		<span class="td-mv">&#183;</span> <span class="td-mv"><?php if($whatsleft > 0) { ?><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo').'/?list='.$this->filters['todolist'].a.'limitstart='.$next; ?>"><?php } ?><?php echo ucfirst(JText::_('COM_PROJECTS_NEXT')); ?> &raquo;<?php if($whatsleft > 0) { ?></a><?php } ?></span>
		<?php } ?>
		<span class="sorting js"><?php echo JText::_('COM_PROJECTS_ORDER_BY'); ?>: 
			<?php if($this->filters['state'] != 1 ) { ?>
			<input type="radio" name="sortby" class="sortoption" value="p.priority ASC" <?php if ($this->filters['sortby'] == 'p.priority ASC') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_TODO_NEWEST'); ?>
			<input type="radio" name="sortby" class="sortoption" value="due DESC, p.duedate ASC" <?php if ($this->filters['sortby'] == 'due DESC, p.duedate ASC') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_DUE_DATE'); ?>
			<?php } else { ?>
			<input type="radio" name="sortby" class="sortoption" value="p.closed DESC" <?php if ($this->filters['sortby'] == 'p.closed DESC') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_TODO_RECENTLY_CLOSED'); ?>
			<input type="radio" name="sortby" class="sortoption" value="p.closed_by DESC, p.closed DESC" <?php if ($this->filters['sortby'] == 'p.closed_by DESC, p.closed DESC') { echo ' checked="checked"'; } ?> /> <?php echo JText::_('COM_PROJECTS_TODO_SORT_CLOSED_BY'); ?>
			<?php } ?>		
		</span>
	</p>
	<?php if (count($this->todos) > 0) {  ?>
	<p class="tips js" id="td-instruct"><?php echo ucfirst(JText::_('COM_PROJECTS_TODO_INSTRUCT')); ?></p>
	<?php } ?>
		<ul class="<?php echo $this->layout; ?>" id="pinboard">
				<?php if (count($this->todos) > 0) {  $order = 1; ?>
				
					<?php foreach($this->todos as $todo) { 
						$class = $todo->color ? 'pin_'.$todo->color : 'pin_grey';
						$index = $todo->assigned_to ? array_search($todo->assigned_to, $team_ids) : 0;

						$overdue = '';
						if($todo->duedate && $todo->duedate != '0000-00-00 00:00:00' && $todo->duedate <= date( 'Y-m-d H:i:s') ) {
							$overdue = ' ('.JText::_('COM_PROJECTS_OVERDUE').')';
						}
						$deletable = ($this->project->role == 1 or $todo->created_by == $this->uid) ? 1 : 0;
						
					?>
						<li class="<?php echo $class; ?> droptarget tdassigned:<?php echo $todo->assigned_to; ?> <?php echo $todo->state==1 ? 'tdclosed' : ''; ?> <?php echo $deletable ? 'deletable' : ''; ?>" id="todo-<?php echo $todo->id; ?>">
							<div id="td-<?php echo $todo->id; ?>">
								<span class="pin handle">&nbsp;</span>
								<?php if($todo->state == 1) { ?>
								<span class="complete">&nbsp;</span>
								<?php } ?>
								<span class="todo-content" id="td-content-<?php echo $todo->id; ?>"><?php echo stripslashes($todo->content); ?></span>
								<span class="todo-options" id="td-options-<?php echo $todo->id; ?>">
								<?php if($todo->state == 1) { ?>
									<span class="todo-assigned"> <?php echo $todo->closedbyname; ?></span> <?php if($todo->closed && $todo->closed != '0000-00-00 00:00:00' ) { echo '<span class="todo-due">'.JText::_('COM_PROJECTS_CHECKED_OFF').' '.JHTML::_('date', $todo->closed, $dateFormat, $tz).'</span>'; } ?>
								<?php } else { ?>	
								<?php echo '<span class="todo-assigned" id="td-assigned-'.$todo->id.'">'.$todo->assignedname.'</span>'; ?> <?php if($todo->duedate && $todo->duedate != '0000-00-00 00:00:00' ) { echo '<span class="todo-due" id="td-due-'.$todo->id.'">'.JText::_('COM_PROJECTS_DUE').' '.JHTML::_('date', $todo->duedate, $dateFormat, $tz).$overdue.'</span>'; }?>
								<?php } ?>	
								</span>
								<input type="hidden" name="idx" id="idx-<?php echo $todo->id; ?>" value="<?php echo $index; ?>" />
								<input type="hidden" name="order" id="order-<?php echo $todo->id; ?>" value="<?php echo $order; ?>" />
								<span class="comment-blurb"><a href="<?php echo JRoute::_('index.php?option='.$this->option.a.$goto.'&active=todo'.a.'action=view').'/?todoid='.$todo->id; ?>" title="<?php echo JText::_('COM_PROJECTS_TODO_VIEW_COMMENTS_AND_EDIT'); ?>"><?php echo $todo->comments; ?>&nbsp;&raquo;</a></span>
							</div>
						</li>
					<?php $order++; } ?>
			<?php } else { ?>
			<li class="todo-empty">	
				<span class="todo-content-empty"><?php echo JText::_('COM_PROJECTS_NO_TODOS').$where.'.'; ?></span>
			</li>
			<?php } ?>
			<li class="clear"></li>
		</ul>
		<?php if($this->filters['state'] != 1 ) { ?>
	</div>
	<?php } ?>
 </div>
</form>
