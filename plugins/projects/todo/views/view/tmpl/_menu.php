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

$url = 'index.php?option=' . $this->option . '&alias=' . $this->project->alias . '&active=todo';

$sortAppend = '';
$sortAppend.= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend.= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend.= '&sortdir=' . urlencode($sortbyDir);

$lists = $this->model->getLists($this->project->id);

$colors = array(
	'orange', 'lightblue', 'green',
	'purple', 'blue', 'black',
	'red', 'yellow', 'pink'
);
$used = array();
if (!empty($lists))
{
	foreach ($lists as $list)
	{
		$used[] = $list->color;
	}
}
$unused = array_diff($colors, $used);
shuffle($unused);

?>
<div class="list-menu">
	<ul class="entries-menu order-options">
		<li>
			<a class="sort-priority<?php if ($this->filters['sortby'] == 'priority') { echo ' active'; } ?>" href="<?php echo JRoute::_($url . $sortAppend . '&sortby=priority' . '&l=' . $this->filters['layout']); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_SORTBY_PRIORITY'); ?>">
				&darr; <?php echo JText::_('PLG_PROJECTS_TODO_SORT_PRIORITY'); ?>
			</a>
		</li>
	<?php if ($this->filters['state']  == 1) { ?>
		<li>
			<a class="sort-due<?php if ($this->filters['sortby'] == 'complete') { echo ' active'; } ?>" href="<?php echo JRoute::_($url . $sortAppend . '&sortby=complete' . '&l=' . $this->filters['layout']); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_SORTBY_COMPLETE'); ?>">
				&darr; <?php echo JText::_('PLG_PROJECTS_TODO_SORT_COMPLETE'); ?>
			</a>
		</li>
	<?php } else { ?>
		<li>
			<a class="sort-complete<?php if ($this->filters['sortby'] == 'due') { echo ' active'; } ?>" href="<?php echo JRoute::_($url . $sortAppend . '&sortby=due' . '&l=' . $this->filters['layout']); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_SORTBY_DUE'); ?>">
				&darr; <?php echo JText::_('PLG_PROJECTS_TODO_SORT_DUE'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
	<ul class="entries-menu view-options">
		<li class="view-pinboard<?php if ($this->filters['layout'] == 'pinboard') { echo ' active'; } ?>"><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist'] . '&l=pinboard' . $sortAppend . '&sortby=' . $this->filters['sortby']); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_LIST_VIEW_PINBOARD'); ?>">&nbsp;</a></li>
		<li class="view-list<?php if ($this->filters['layout'] == 'list') { echo ' active'; } ?>"><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist'] . '&l=list' . $sortAppend . '&sortby=' . $this->filters['sortby']); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_LIST_VIEW_LIST'); ?>">&nbsp;</a></li>
	</ul>
	<ul class="entries-menu filter-options">
		<li><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . urlencode($sortbyDir) . '&sortby=' . $this->filters['sortby'] . '&mine=0&state=0'); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?>" class="filter-active<?php if (!$this->filters['mine'] && !$this->filters['state']) { echo ' active'; } ?>"><?php echo JText::_('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?></a></li>
		<li><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . urlencode($sortbyDir) . '&sortby=' . $this->filters['sortby'] . '&mine=1'); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_FILTER_MINE'); ?>" class="filter-mine<?php if ($this->filters['mine'] == 1) { echo ' active'; } ?>"><?php echo JText::_('PLG_PROJECTS_TODO_FILTER_MINE'); ?></a></li>
		<li><a href="<?php echo JRoute::_($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . urlencode($sortbyDir) . '&sortby=' . $this->filters['sortby'] . '&state=1'); ?>" title="<?php echo JText::_('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?>" class="filter-complete<?php if ($this->filters['state'] == 1) { echo ' active'; } ?>"><?php echo JText::_('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?></a></li>
	</ul>
	<?php if (!$this->filters['todolist']) {  ?>
	<div class="list-selector" id="list-selector">
		<span id="pinner"><?php echo JText::_('PLG_PROJECTS_TODO_ON_LIST'); ?><span class="show-options">&nbsp;</span></span>
		<div id="pinoptions">
			<ul>
				<?php foreach ($lists as $list) {
					$class = $list->color ? 'pin_' . $list->color : 'pin_grey';
				?>
					<li>
						<span class="<?php echo $class; ?>"><a href="<?php echo JRoute::_($url . '&list=' . $list->color . '&l=' . $this->filters['layout'] . '&sortby=' . $this->filters['sortby'] . $sortAppend); ?>"><?php echo stripslashes($list->todolist); ?></a></span>
						</label>
					</li>
				<?php } ?>
				<?php if (!empty($unused)) { // can add a list
					$newcolor = $unused[0];
				?>
				<li class="newcolor">
					<span class="pin pin_<?php echo $newcolor; ?>">&nbsp;</span>
					<input type="hidden" name="newcolor" value="<?php echo $newcolor; ?>" />
					<input type="text" name="newlist" placeholder="<?php echo JText::_('PLG_PROJECTS_TODO_ADD_NEW_LIST'); ?>" value="" maxlength="50" class="newlist-input" />
					<input type="submit" class="btn" value="<?php echo JText::_('PLG_PROJECTS_TODO_ADD'); ?>" class="todo-submit" />
				</li>
			<?php }  ?>
			</ul>
		</div>
	</div>
	<?php } ?>
</div>