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

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_NOTES'), 'user');
if ($canDo->get('core.create'))
{
	Toolbar::addNew('note.add');
}

if ($canDo->get('core.edit'))
{
	Toolbar::editList('note.edit');
}

if ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::publish('notes.publish', 'JTOOLBAR_PUBLISH', true);
	Toolbar::unpublish('notes.unpublish', 'JTOOLBAR_UNPUBLISH', true);

	Toolbar::divider();
	Toolbar::archiveList('notes.archive');
	Toolbar::checkin('notes.checkin');
}

if ($this->filters['state'] == -2 && $canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'notes.delete', 'JTOOLBAR_EMPTY_TRASH');
	Toolbar::divider();
}
elseif ($canDo->get('core.edit.state'))
{
	Toolbar::trash('notes.trash');
	Toolbar::divider();
}

if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_members');
	Toolbar::divider();
}
Toolbar::help('notes');
?>
<nav role="navigation" class="sub sub-navigation">
	<ul>
		<li>
			<a<?php if ($this->controller == 'notes') { echo ' class="active"'; } ?> href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=notes'); ?>"><?php echo Lang::txt('COM_MEMBERS_SUBMENU_NOTES'); ?></a>
		</li>
		<li>
			<a href="<?php echo Route::url('index.php?option=com_categories&extension=com_users'); ?>"><?php echo Lang::txt('COM_MEMBERS_SUBMENU_NOTE_CATEGORIES'); ?></a>
		</li>
	</ul>
</nav>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_IN_NOTE_TITLE'); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>

			<div class="col span6">
				<select name="filter_category_id" id="filter_category_id" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo Html::select('options', Html::category('options', 'com_users.notes'), 'value', 'text', $this->filters['category_id']); ?>
				</select>

				<select name="filter_published" class="inputbox" onchange="this.form.submit()">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->filters['state'], true); ?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="toggle" value="" class="checklist-toggle" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_MEMBERS_USER_HEADING', 'u.name', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_MEMBERS_SUBJECT_HEADING', 'a.subject', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'COM_MEMBERS_CATEGORY_HEADING', 'c.title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JSTATUS', 'a.state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_MEMBERS_REVIEW_HEADING', 'a.review_time', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
				<th class="nowrap priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->rows->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		foreach ($this->rows as $row) : ?>
			<?php $canChange = User::authorise('core.edit.state', $this->option); ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center checklist">
					<?php echo Html::grid('id', $i, $row->get('id')); ?>
				</td>
				<td>
					<?php if ($row->get('checked_out')) : ?>
						<?php echo Html::grid('checkedout', $i, $row->editor, $row->get('checked_out_time')); ?>
					<?php endif; ?>
					<?php if ($canDo->get('core.edit')) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id'));?>">
							<?php echo $this->escape($row->member->get('name')); ?></a>
					<?php else : ?>
						<?php echo $this->escape($row->member->get('name')); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($row->get('subject')) : ?>
						<?php echo $this->escape($row->get('subject')); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_MEMBERS_EMPTY_SUBJECT'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-5">
					<?php /*if ($row->get('catid') && $item->cparams->get('image')) : ?>
						<?php echo Html::users('image', $item->cparams->get('image')); ?>
					<?php endif;*/ ?>
					<?php echo $this->escape($row->category->get('title')); ?>
				</td>
				<td class="center priority-4">
					<?php echo Html::grid('published', $row->get('state'), $i, 'notes.', $canChange, 'cb', $row->get('publish_up'), $row->get('publish_down')); ?>
				</td>
				<td class="center">
					<?php if ($row->get('review_time') && $row->get('review_time') != '0000-00-00 00:00:00') : ?>
						<?php echo $this->escape($row->get('review_time')); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_MEMBERS_EMPTY_REVIEW'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-6">
					<?php echo (int) $row->get('id'); ?>
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
