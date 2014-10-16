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
	<table class="listing" id="todo-table">
		<thead>
			<tr>
				<th class="checkbox"><?php echo JText::_('PLG_PROJECTS_TODO_COLUMN_ORDER'); ?></th>
				<th class="primarycolumn"><?php echo JText::_('PLG_PROJECTS_TODO_COLUMN_ITEM'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_TODO_COLUMN_ASSIGNED'); ?></th>
				<th><?php echo ($this->filters['state']) ? JText::_('PLG_PROJECTS_TODO_COLUMN_COMPLETED') : JText::_('PLG_PROJECTS_TODO_COLUMN_DUE'); ?></th>
				<th><?php echo JText::_('PLG_PROJECTS_TODO_COLUMN_COMMENTS'); ?></th>
				<th></th>
			</tr>
		</thead>
		<tbody id="todo-table-body" class="allow-sort">
		<?php if (count($rows) > 0) {  $order = 1; ?>
			<?php foreach ($rows as $row) {
				$color = $row->get('color');
				$class = $color ? 'pin_' . $color : 'pin_grey';

				$overdue = $row->isOverdue();
				$oNote = $overdue ? '<span class="block">(' . JText::_('PLG_PROJECTS_TODO_OVERDUE') . ')</span>' : '';
			?>
			<tr class="<?php echo $class; ?>" id="todo-<?php echo $row->get('id'); ?>">
				<td><span class="ordernum"><?php echo $order; ?></span></td>
				<td><a href="<?php echo JRoute::_($url . '&action=view').'/?todoid=' . $row->get('id') ; ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_TODO_VIEW_COMMENTS_AND_EDIT'); ?>"><?php echo \Hubzero\Utility\String::truncate($row->get('content'), 120); ?></a></td>
				<td class="mini faded"><?php echo $row->isComplete() ? $row->closer('name') : $row->owner('name'); ?></td>
				<td class="mini nowrap"><?php echo $row->isComplete() ? $row->closed('date') : $row->due('date') . $oNote;?></td>
				<td class="mini"><a href="<?php echo JRoute::_($url . '&action=view').'/?todoid=' . $row->get('id') ; ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_TODO_VIEW_COMMENTS_AND_EDIT'); ?>"><?php echo $row->comments('count'); ?></a></td>
				<td><?php if (!$row->isComplete()) { ?><a href="<?php echo JRoute::_($url . '&action=changestate') . '/?todoid=' . $row->get('id') . '&amp;state=1'; ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_CHECK_OFF'); ?>" class="unchecked confirm-checkoff">&nbsp;</a><?php } ?></td>
			</tr>
			<?php $order++; } ?>
		<?php } else { ?>
			<tr>
				<td colspan="6"><p class="noresults"><?php echo JText::_('PLG_PROJECTS_TODO_NO_TODOS') . $where . '.'; ?></p></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
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
