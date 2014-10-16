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
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.	 See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

$this->css('jquery.datepicker.css', 'system')
	 ->css('jquery.timepicker.css', 'system')
	 ->css()
	 ->js('jquery.timepicker', 'system')
	 ->js();

$total = $this->model->entries('count', $this->filters);
$rows  = $this->model->entries('list', $this->filters);
$listName = $this->model->getListName($this->project->id, $this->filters);

$whatsleft = $total - $this->filters['start'] - $this->filters['limit'];
$team_ids = array('0' => '');
$which = $this->filters['state'] == 1 ? strtolower(JText::_('PLG_PROJECTS_TODO_COMPLETED')) : JText::_('PLG_PROJECTS_TODO_OUTSTANDING');
$where = $listName ? ' '.JText::_('PLG_PROJECTS_TODO_TODO_ON_THIS_LIST') : '';
$where.= $this->filters['mine'] == 1 ? ' '.JText::_('PLG_PROJECTS_TODO_IN_MY_TODOS') : '';

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=todo';

?>
<form action="<?php echo JRoute::_($url); ?>" method="post" id="plg-form" >
	<?php // View header
	$this->view('_header')
		 ->set('option', $this->option)
		 ->set('project', $this->project)
		 ->set('model', $this->model)
		 ->set('filters', $this->filters)
		 ->set('title', $this->title)
		 ->set('listName', $listName)
		 ->display();
	?>
	<?php if (!$this->filters['state']) { ?>
	<ul id="page_options" class="pluginOptions">
		<li>
			<a class="icon-add add btn showinbox"  href="<?php echo JRoute::_($url . '&action=new&list=' . $this->filters['todolist']); ?>">
				<?php echo JText::_('PLG_PROJECTS_TODO_ADD_TODO'); ?>
			</a>
		</li>
	</ul>
	<?php } ?>
	<div class="container">
	<?php
		// View menu
		$this->view('_menu')
			 ->set('option', $this->option)
			 ->set('project', $this->project)
			 ->set('model', $this->model)
			 ->set('filters', $this->filters)
			 ->display();
	?>
	<ul class="pinboard allow-sort" id="pinboard">
			<?php if (count($rows) > 0) {  $order = 1; ?>
				<?php foreach ($rows as $row) {

					$color = $row->get('color');
					$class = $color ? 'pin_' . $color : 'pin_grey';

					$overdue = $row->isOverdue();
					$oNote = $overdue ? ' ('.JText::_('PLG_PROJECTS_TODO_OVERDUE').')' : '';
				?>
					<li class="<?php echo $class; ?> droptarget <?php echo $row->isComplete() ? 'tdclosed' : ''; ?>" id="todo-<?php echo $row->get('id'); ?>">
						<div id="td-<?php echo $row->get('id'); ?>">
							<span class="pin handle">&nbsp;</span>
							<?php if ($row->isComplete()) { ?>
							<span class="complete">&nbsp;</span>
							<?php } ?>
							<span class="todo-content" id="td-content-<?php echo $row->get('id'); ?>"><?php echo \Hubzero\Utility\String::truncate($row->get('content'), 150); ?></span>
							<span class="todo-options" id="td-options-<?php echo $row->get('id'); ?>">
							<?php if ($row->isComplete()) { ?>
								<span class="todo-assigned"> <?php echo $row->closer('name'); ?></span> <?php if ($row->isComplete()) { echo '<span class="todo-due">' . JText::_('PLG_PROJECTS_TODO_CHECKED_OFF') . ' ' . $row->closed('date') . '</span>'; } ?>
							<?php } else { ?>
							<?php echo '<span class="todo-assigned" id="td-assigned-' . $row->get('id') . '">' . $row->owner('name') . '</span>'; ?> <?php if ($row->due()) { echo '<span class="todo-due" id="td-due-' . $row->get('id') . '">' . JText::_('PLG_PROJECTS_TODO_DUE') . ' ' . $row->due('date') . $oNote . '</span>'; } ?>
							<?php } ?>
							</span>
							<input type="hidden" name="order" id="order-<?php echo $row->get('id'); ?>" value="<?php echo $order; ?>" />
							<span class="comment-blurb"><a href="<?php echo JRoute::_($url . '&action=view').'/?todoid=' . $row->get('id') ; ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_TODO_VIEW_COMMENTS_AND_EDIT'); ?>"><?php echo $row->comments('count'); ?>&nbsp;&raquo;</a></span>
						</div>
					</li>
				<?php $order++; } ?>
		<?php } elseif (!$this->filters['state']) {	 ?>
		<li class="todo-add">
			<span><a class="item-add showinbox" href="<?php echo JRoute::_($url . '&action=new&list=' . $this->filters['todolist']); ?>">
				<?php echo JText::_('PLG_PROJECTS_TODO_ADD_TODO'); ?>
			</a></span>
		</li>
		<?php } else {	?>
			<li class="todo-empty"><?php echo JText::_('PLG_PROJECTS_TODO_NO_TODOS').$where.'.'; ?></li>
		<?php } ?>
		<li class="clear"></li>
	</ul>
	<?php
		// View footer
		$this->view('_footer')
			 ->set('option', $this->option)
			 ->set('project', $this->project)
			 ->set('model', $this->model)
			 ->set('filters', $this->filters)
			 ->set('uid', $this->uid)
			 ->display();
	?>
	</div>
</form>
