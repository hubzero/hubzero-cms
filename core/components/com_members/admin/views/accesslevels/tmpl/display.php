<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_ACCESSLEVELS'), 'user');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
	Toolbar::divider();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::divider();
}
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_members');
	Toolbar::divider();
}
Toolbar::help('levels');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', $this->option);
$saveOrder = $listOrder == 'a.ordering';

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'accessgroups') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=accessgroups'); ?>"><?php echo Lang::txt('COM_MEMBERS_ACCESSGROUPS'); ?></a>
		</li>
		<li>
			<a<?php if ($this->controller == 'accesslevels') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=accesslevels'); ?>"><?php echo Lang::txt('COM_MEMBERS_ACCESSLEVELS'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_MEMBERS_SEARCH_ACCESS_LEVELS'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_TITLE_LEVELS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="priority-3">
					<?php echo Lang::txt('JGRID_HEADING_ID'); ?>
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_MEMBERS_HEADING_LEVEL_NAME', 'a.title', $this->filters['sort_Dir'], $this->filters['sort']); ?>
				</th>
				<th>
					<?php echo Html::grid('sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $this->filters['sort_Dir'], $this->filters['sort']); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->rows); ?>
					<?php endif; ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$n = $this->rows->count();
		foreach ($this->rows as $row) :
			$ordering  = ($listOrder == 'a.ordering');
			$canCreate = User::authorise('core.create', $this->option);
			$canEdit   = User::authorise('core.edit', $this->option);
			$canChange = User::authorise('core.edit.state', $this->option);
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $row->get('id')); ?>
				</td>
				<td class="center priority-3">
					<?php echo (int) $row->get('id'); ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
							<?php echo $this->escape($row->get('title')); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($row->get('title')); ?>
					<?php endif; ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo ($i > 0) ? Html::grid('orderUp', $i, 'orderup', '', 'JLIB_HTML_MOVE_UP', true, 'cb') : '&#160;'; ?></span>
								<span><?php echo ($i < ($n - 1)) ? Html::grid('orderDown', $i, 'orderdown', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') : '&#160;'; ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo ($i > 0) ? Html::grid('orderUp', $i, 'orderdown', '', 'JLIB_HTML_MOVE_UP', true, 'cb') : '&#160;'; ?></span>
								<span><?php echo ($i < ($n - 1)) ? Html::grid('orderDown', $i, 'orderup', '', 'JLIB_HTML_MOVE_DOWN', true, 'cb') : '&#160;'; ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $row->get('ordering'); ?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $row->get('ordering'); ?>
					<?php endif; ?>
				</td>
			</tr>
		<?php
			$i++;
		endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>
