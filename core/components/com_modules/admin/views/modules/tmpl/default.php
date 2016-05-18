<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_modules
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_HZEXEC_') or die();

Html::behavior('tooltip');
Html::behavior('multiselect');

$client    = $this->state->get('filter.client_id') ? 'administrator' : 'site';
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$canOrder  = User::authorise('core.edit.state', 'com_modules');
$saveOrder = $listOrder == 'ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_modules'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_MODULES_MODULES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<?php echo Html::select('options', ModulesHelper::getClientOptions(), 'value', 'text', $this->state->get('filter.client_id'));?>
			</select>

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', ModulesHelper::getStateOptions(), 'value', 'text', $this->state->get('filter.state'));?>
			</select>

			<select name="filter_position" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MODULES_OPTION_SELECT_POSITION');?></option>
				<?php echo Html::select('options', ModulesHelper::getPositions($this->state->get('filter.client_id')), 'value', 'text', $this->state->get('filter.position'));?>
			</select>

			<select name="filter_module" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MODULES_OPTION_SELECT_MODULE');?></option>
				<?php echo Html::select('options', ModulesHelper::getModules($this->state->get('filter.client_id')), 'value', 'text', $this->state->get('filter.module'));?>
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

	<table class="adminlist" id="modules-mgr">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" onclick="checkAll(this)" />
				</th>
				<th scope="col" class="title">
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2 left">
					<?php echo Html::grid('sort',  'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order',  $this->items, 'filesave.png', 'modules.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th scope="col" class="priority-3 left">
					<?php echo Html::grid('sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort',  'COM_MODULES_HEADING_PAGES', 'pages', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-6">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$ordering   = ($listOrder == 'ordering');
			$canCreate  = User::authorise('core.create',     'com_modules');
			$canEdit    = User::authorise('core.edit',       'com_modules');
			$canCheckin = User::authorise('core.manage',     'com_checkin') || $item->checked_out == User::get('id')|| $item->checked_out==0;
			$canChange  = User::authorise('core.edit.state', 'com_modules') && $canCheckin;
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, 'modules.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=com_modules&task=module.edit&id='.(int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
							<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<?php if (!empty($item->note)) : ?>
					<p class="smallsub">
						<?php echo Lang::txt('JGLOBAL_LIST_NOTE', $this->escape($item->note));?></p>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo Html::modules('state', $item->published, $i, $canChange, 'cb'); ?>
				</td>
				<td class="priority-2 left">
					<?php if ($item->position) : ?>
						<?php echo $item->position; ?>
					<?php else : ?>
						<?php echo ':: ' . Lang::txt('JNONE') . ' ::'; ?>
					<?php endif; ?>
				</td>
				<td class="priority-3 order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->position == $item->position), 'modules.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->position == $item->position), 'modules.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, (@$this->items[$i-1]->position == $item->position), 'modules.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, (@$this->items[$i+1]->position == $item->position), 'modules.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled; ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="priority-3 left">
					<?php echo $item->name;?>
				</td>
				<td class="priority-4 center">
					<?php echo $item->pages; ?>
				</td>
				<td class="priority-4 center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="priority-5 center">
					<?php if ($item->language==''):?>
						<?php echo Lang::txt('JDEFAULT'); ?>
					<?php elseif ($item->language=='*'):?>
						<?php echo Lang::txt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="priority-6 center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form.is user is allowed ?>
	<?php if (User::authorise('core.create', 'com_modules') && User::authorise('core.edit', 'com_modules') && User::authorise('core.edit.state', 'com_modules')) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif;?>

	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
