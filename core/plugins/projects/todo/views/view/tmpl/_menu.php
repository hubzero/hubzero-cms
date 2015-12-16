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

$url = 'index.php?option=' . $this->option . '&alias=' . $this->model->get('alias') . '&active=todo';

$sortAppend = '';
$sortAppend.= $this->filters['mine'] == 1 ? '&mine=1' : ''; // show mine?
$sortAppend.= $this->filters['state'] == 1 ? '&state=1' : ''; // show complete?

$sortbyDir  = $this->filters['sortdir'] == 'ASC' ? 'DESC' : 'ASC';
$sortAppend.= '&sortdir=' . urlencode($sortbyDir);

$lists = $this->todo->getLists($this->model->get('id'));

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
			<a class="sort-priority<?php if ($this->filters['sortby'] == 'priority') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=priority' . '&l=' . $this->filters['layout']); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_PRIORITY'); ?>">
				&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_PRIORITY'); ?>
			</a>
		</li>
	<?php if ($this->filters['state']  == 1) { ?>
		<li>
			<a class="sort-due<?php if ($this->filters['sortby'] == 'complete') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=complete' . '&l=' . $this->filters['layout']); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_COMPLETE'); ?>">
				&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_COMPLETE'); ?>
			</a>
		</li>
	<?php } else { ?>
		<li>
			<a class="sort-complete<?php if ($this->filters['sortby'] == 'due') { echo ' active'; } ?>" href="<?php echo Route::url($url . $sortAppend . '&sortby=due' . '&l=' . $this->filters['layout']); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_SORTBY_DUE'); ?>">
				&darr; <?php echo Lang::txt('PLG_PROJECTS_TODO_SORT_DUE'); ?>
			</a>
		</li>
	<?php } ?>
	</ul>
	<ul class="entries-menu view-options">
		<li class="view-pinboard<?php if ($this->filters['layout'] == 'pinboard') { echo ' active'; } ?>"><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist'] . '&l=pinboard&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby']); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_LIST_VIEW_PINBOARD'); ?>">&nbsp;</a></li>
		<li class="view-list<?php if ($this->filters['layout'] == 'list') { echo ' active'; } ?>"><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist'] . '&l=list&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby']); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_LIST_VIEW_LIST'); ?>">&nbsp;</a></li>
	</ul>
	<ul class="entries-menu filter-options">
		<li><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&mine=0&state=0'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?>" class="filter-active<?php if (!$this->filters['mine'] && !$this->filters['state']) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_ACTIVE'); ?></a></li>
		<li><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&mine=1'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE'); ?>" class="filter-mine<?php if ($this->filters['mine'] == 1) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_MINE'); ?></a></li>
		<li><a href="<?php echo Route::url($url . '&list=' . $this->filters['todolist'] . '&l=' . $this->filters['layout'] . '&sortdir=' . $this->filters['sortdir'] . '&sortby=' . $this->filters['sortby'] . '&state=1'); ?>" title="<?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?>" class="filter-complete<?php if ($this->filters['state'] == 1) { echo ' active'; } ?>"><?php echo Lang::txt('PLG_PROJECTS_TODO_FILTER_COMPLETE'); ?></a></li>
	</ul>
	<?php if (!$this->filters['todolist']) {  ?>
	<div class="list-selector" id="list-selector">
		<span id="pinner"><?php echo Lang::txt('PLG_PROJECTS_TODO_ON_LIST'); ?><span class="show-options">&nbsp;</span></span>
		<div id="pinoptions">
			<ul>
				<?php foreach ($lists as $list) {
					$class = $list->color ? 'pin_' . $list->color : 'pin_grey';
				?>
					<li>
						<span class="<?php echo $class; ?>"><a href="<?php echo Route::url($url . '&list=' . $list->color . '&l=' . $this->filters['layout'] . '&sortby=' . $this->filters['sortby'] . '&sortdir=' . $this->filters['sortdir']); ?>"><?php echo stripslashes($list->todolist); ?></a></span>
						</label>
					</li>
				<?php } ?>
				<?php if (!empty($unused)) { // can add a list
					$newcolor = $unused[0];
				?>
				<li class="newcolor">
					<span class="pin pin_<?php echo $newcolor; ?>">&nbsp;</span>
					<input type="hidden" name="newcolor" value="<?php echo $newcolor; ?>" />
					<?php echo Html::input('token');?>
					<input type="text" name="newlist" placeholder="<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD_NEW_LIST'); ?>" value="" maxlength="50" class="newlist-input" />
					<input type="submit" class="btn" value="<?php echo Lang::txt('PLG_PROJECTS_TODO_ADD'); ?>" class="todo-submit" />
				</li>
			<?php }  ?>
			</ul>
		</div>
	</div>
	<?php } ?>
</div>
