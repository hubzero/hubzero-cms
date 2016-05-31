<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_categories
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$userId    = User::get('id');
$extension = $this->escape($this->state->get('filter.extension'));
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.lft');
$saveOrder = ($listOrder == 'a.lft' && $listDirn == 'asc');
?>
<form action="<?php echo Route::url('index.php?option=com_categories&view=categories');?>" method="post" name="adminForm" id="adminForm">

	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_level" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_MAX_LEVELS');?></option>
				<?php echo Html::select('options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'));?>
			</select>

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo Html::select('options', Html::contentlanguage('existing', true, true), 'value', 'text', $this->state->get('filter.language'));?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo Html::grid('order',  $this->items, 'filesave.png', 'categories.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ACCESS', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $this->state->get('list.direction'), $this->state->get('list.ordering')); ?>
				</th>
				<th class="priority-4 nowrap">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
			<?php
			$originalOrders = array();
			foreach ($this->items as $i => $item) :
				$orderkey   = array_search($item->id, $this->ordering[$item->parent_id]);
				$canEdit    = User::authorise('core.edit',       $extension.'.category.'.$item->id);
				$canCheckin = User::authorise('core.admin',      'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn = User::authorise('core.edit.own',   $extension.'.category.'.$item->id) && $item->created_user_id == $userId;
				$canChange  = User::authorise('core.edit.state', $extension.'.category.'.$item->id) && $canCheckin;
			?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo Html::grid('id', $i, $item->id); ?>
					</td>
					<td>
						<?php
							if ($item->level-1  > 0)
							{
								echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1);
							}
						?>
						<?php if ($item->checked_out) : ?>
							<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, 'categories.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo Route::url('index.php?option=com_categories&task=category.edit&id='.$item->id.'&extension='.$extension);?>">
								<?php echo $this->escape($item->title); ?></a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
						<p class="smallsub" title="<?php echo $this->escape($item->path);?>">
							<?php
								if ($item->level-1  > 0)
								{
									echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1);
								}
							?>
							<?php if (empty($item->note)) : ?>
								<?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo Lang::txt('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?></p>
					</td>
					<td class="priority-2 center">
						<?php echo Html::grid('published', $item->published, $i, 'categories.', $canChange);?>
					</td>
					<td class="priority-2 order">
						<?php if ($canChange) : ?>
							<?php if ($saveOrder) : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php $originalOrders[] = $orderkey + 1; ?>
						<?php else : ?>
							<?php echo $orderkey + 1;?>
						<?php endif; ?>
					</td>
					<td class="priority-3 center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="priority-4 center nowrap">
					<?php if ($item->language=='*'):?>
						<?php echo Lang::txt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
					<?php endif;?>
					</td>
					<td class="priority-4 center">
						<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
							<?php echo (int) $item->id; ?></span>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php //Load the batch processing form. ?>
	<?php if (User::authorise('core.create', $extension) & User::authorise('core.edit', $extension) && User::authorise('core.edit.state', $extension)) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif;?>

	<input type="hidden" name="extension" value="<?php echo $extension;?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
	<?php echo Html::input('token'); ?>
</form>
