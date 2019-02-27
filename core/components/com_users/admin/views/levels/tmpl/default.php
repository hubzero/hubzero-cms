<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_users
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');

// Load the tooltip behavior.
Html::behavior('tooltip');
Html::behavior('multiselect');

$user		= JFactory::getUser();
$listOrder	= $this->escape($this->state->get('list.ordering'));
$listDirn	= $this->escape($this->state->get('list.direction'));
$canOrder	= $user->authorise('core.edit.state', 'com_users');
$saveOrder	= $listOrder == 'a.ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_users&view=levels');?>" method="post" id="adminForm" name="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('COM_USERS_SEARCH_ACCESS_LEVELS'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_USERS_SEARCH_TITLE_LEVELS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_RESET'); ?></button>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th class="priority-3">
					<?php echo Lang::txt('JGRID_HEADING_ID'); ?>
				</th>
				<th class="left">
					<?php echo Html::grid('sort', 'COM_USERS_HEADING_LEVEL_NAME', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'levels.saveorder'); ?>
					<?php endif; ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="4">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php $count = count($this->items); ?>
		<?php foreach ($this->items as $i => $item) :
			$ordering  = ($listOrder == 'a.ordering');
			$canCreate = $user->authorise('core.create', 'com_users');
			$canEdit   = $user->authorise('core.edit', 'com_users');
			$canChange = $user->authorise('core.edit.state', 'com_users');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td class="center priority-3">
					<?php echo (int) $item->id; ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
					<a href="<?php echo Route::url('index.php?option=com_users&task=level.edit&id='.$item->id);?>">
						<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'levels.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $count, true, 'levels.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'levels.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $count, true, 'levels.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
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
