<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Members\Helpers\Admin::getActions('component');

// Menu
Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . Lang::txt('COM_MEMBERS_NOTES'), 'user');
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}

if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}

if ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::publish();
	Toolbar::unpublish();

	Toolbar::divider();
	Toolbar::archiveList();
	Toolbar::checkin();
}

if ($this->filters['state'] == -2 && $canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'delete', 'JTOOLBAR_EMPTY_TRASH');
	Toolbar::divider();
}
elseif ($canDo->get('core.edit.state'))
{
	Toolbar::trash();
	Toolbar::divider();
}

if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_members');
	Toolbar::divider();
}
Toolbar::help('notes');
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="grid">
			<div class="col span6">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MEMBERS_SEARCH_IN_NOTE_TITLE'); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>

			<div class="col span6">
				<select name="filter_category_id" id="filter_category_id" class="inputbox filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
					<?php echo Html::select('options', Html::category('options', 'com_members'), 'value', 'text', $this->filters['category_id']); ?>
				</select>

				<select name="filter_published" class="inputbox filter filter-submit">
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
					<input type="checkbox" name="toggle" value="" class="checklist-toggle" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
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
	<input type="hidden" name="filter_order" value="<?php echo $this->escape($this->filters['sort']); ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->escape($this->filters['sort_Dir']); ?>" />

	<?php echo Html::input('token'); ?>
</form>
