<?php
/**
 * @package		HUBzero CMS
 * @author		Alissa Nedossekina <alisa@purdue.edu>
 * @copyright	Copyright 2005-2009 HUBzero Foundation, LLC.
 * @license		http://opensource.org/licenses/MIT MIT
 *
 * Copyright 2005-2009 HUBzero Foundation, LLC.
 * All rights reserved.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 */

// No direct access
defined('_HZEXEC_') or die();

$url = 'index.php?option=com_members&id=' . $this->uid . '&active=todo';

$sortAppend = '';
$sortAppend.= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend.= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend.= '&sortdir=' . urlencode($sortbyDir);

$rows = $this->todo->entries('list', $this->filters);

?>

<div class="list-menu">
	<nav class="entries-filters">
		<ul class="entries-menu order-options">
			<li>
				<a class="sort-content<?php if ($this->filters['sortby'] == 'content') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=content'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_CONTENT'); ?>">
					&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_CONTENT'); ?>
				</a>
			</li>
			<li>
				<a class="sort-project<?php if ($this->filters['sortby'] == 'project') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=project'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_PROJECT'); ?>">
					&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_PROJECT'); ?>
				</a>
			</li>
			<?php if ($this->filters['state']  == 1) { ?>
			<li>
				<a class="sort-due<?php if ($this->filters['sortby'] == 'complete') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=complete'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_COMPLETE'); ?>">
					&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_COMPLETE'); ?>
				</a>
			</li>
		<?php } else { ?>
			<li>
				<a class="sort-complete<?php if ($this->filters['sortby'] == 'due') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=due'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_DUE'); ?>">
					&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_DUE'); ?>
				</a>
			</li>
			<?php } ?>
		</ul>

		<ul class="entries-menu filter-options">
			<li><a href="<?php echo Route::url($url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&mine=0&state=0'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?>" class="filter-active<?php if (!$this->filters['mine'] && !$this->filters['state']) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?></a></li>
			<li><a href="<?php echo Route::url($url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&mine=1'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE'); ?>" class="filter-mine<?php if ($this->filters['mine'] == 1) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE'); ?></a></li>
			<li><a href="<?php echo Route::url($url . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&state=1'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?>" class="filter-complete<?php if ($this->filters['state'] == 1) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?></a></li>
		</ul>
	</nav>
</div>
<table class="listing entries" id="todo-table">
	<thead>
		<tr>
			<th class="checkbox"><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ORDER'); ?></th>
			<th class="primarycolumn"><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ITEM'); ?></th>
			<th class="primarycolumn"><?php echo Lang::txt('PLG_PROJECTS_TODO_PROJECT'); ?></th>
			<th><?php echo Lang::txt('PLG_PROJECTS_TODO_COLUMN_ASSIGNED'); ?></th>
			<th><?php echo ($this->filters['state']) ? Lang::txt('PLG_PROJECTS_TODO_COLUMN_COMPLETED') : Lang::txt('PLG_PROJECTS_TODO_COLUMN_DUE'); ?></th>
			<th></th>
		</tr>
	</thead>
	<tbody id="todo-table-body" class="allow-sort">
	<?php if (count($rows) > 0) {  $order = 1; ?>
		<?php foreach ($rows as $row) {
			$color = $row->get('color');

			$overdue = $row->isOverdue();
			$oNote = $overdue ? ' <span class="block">(' . Lang::txt('PLG_PROJECTS_TODO_OVERDUE') . ')</span>' : '';
		?>
		<tr class="pin_grey" id="todo-<?php echo $row->get('id'); ?>">
			<td><span class="ordernum"><?php echo $order; ?></span></td>
			<td><a href="<?php echo Route::url($row->project()->link('todo') . '&action=view&todoid=' . $row->get('id')); ?>"><?php echo \Hubzero\Utility\String::truncate($row->get('content'), 200); ?></a>
				<span class="block mini faded"><?php echo Lang::txt('PLG_PROJECTS_TODO_CREATED') . ' ' . $row->created('date') . ' ' . strtolower(Lang::txt('PLG_PROJECTS_TODO_BY')) . ' ' . $row->creator('name'); ?>
					| <?php echo Lang::txt('PLG_PROJECTS_TODO_COMMENTS'); ?>: <a href="<?php echo Route::url($row->project()->link('todo') . '&action=view&todoid=' . $row->get('id')); ?>"><?php echo $row->comments('count'); ?></a></span>
			</td>
			<td><a href="<?php echo Route::url($row->project()->link('todo')); ?>"><?php echo $row->project('title'); ?></a></td>
			<td class="mini faded"><?php echo $row->isComplete() ? $row->closer('name') : $row->owner('name'); ?></td>
			<td class="mini nowrap"><?php echo $row->isComplete() ? $row->closed('date') : $row->due('date') . $oNote;?></td>
			<td></td>
		</tr>
		<?php $order++; } ?>
	<?php } else { ?>
		<tr>
			<td colspan="6"><p class="noresults"><?php echo Lang::txt('PLG_PROJECTS_TODO_NO_TODOS'); ?></p></td>
		</tr>
	<?php } ?>
	</tbody>
</table>
