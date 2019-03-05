<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE
 */

defined('_HZEXEC_') or die();

/* @var $this UsersViewNotes */

Html::behavior('tooltip');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn = $this->escape($this->state->get('list.direction'));
$canEdit = User::authorise('core.edit', 'com_users');
?>
<form action="<?php echo Route::url('index.php?option=com_users&view=notes');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_USERS_SEARCH_IN_NOTE_TITLE'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-submit"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_category_id" id="filter_category_id" class="inputbox filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo Html::select('options', Html::category('options', 'com_users.notes'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_published" class="inputbox filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="toggle" value="" class="checklist-toggle" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_USERS_USER_HEADING', 'u.name', $listDirn, $listOrder); ?>
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_USERS_SUBJECT_HEADING', 'a.subject', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'COM_USERS_CATEGORY_HEADING', 'c.title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_USERS_REVIEW_HEADING', 'a.review_time', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) : ?>
			<?php $canChange = User::authorise('core.edit.state', 'com_users'); ?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center checklist">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=com_users&task=note.edit&id='.$item->id);?>">
							<?php echo $this->escape($item->user_name); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->user_name); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php if ($item->subject) : ?>
						<?php echo $this->escape($item->subject); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_USERS_EMPTY_SUBJECT'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-5">
					<?php if ($item->catid && $item->cparams->get('image')) : ?>
						<?php echo Html::users('image', $item->cparams->get('image')); ?>
					<?php endif; ?>
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="center priority-4">
					<?php echo Html::grid('published', $item->state, $i, 'notes.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center">
					<?php if (intval($item->review_time)) : ?>
						<?php echo $this->escape($item->review_time); ?>
					<?php else : ?>
						<?php echo Lang::txt('COM_USERS_EMPTY_REVIEW'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-6">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
