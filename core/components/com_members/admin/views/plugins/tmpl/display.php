<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('tooltip');
Html::behavior('multiselect');

$canDo = Components\Members\Helpers\Permissions::getActions('component');

Toolbar::title(Lang::txt('Members') . ': ' . Lang::txt('Plugins'), 'members');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
}

include_once Component::path('com_plugins') . '/helpers/plugins.php';

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', 'com_plugins');
$saveOrder = $listOrder == 'ordering';
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Components\Plugins\Helpers\Plugins::stateOptions(), 'value', 'text', $this->filters['state'], true);?>
			</select>

			<select name="filter_access" class="inputbox filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th scope="col" class="title">
					<?php echo Html::grid('sort', 'Plug-in Name', 'name', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JSTATUS', 'enabled', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'plugins.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th scope="col" class="priority-3 nowrap">
					<?php echo Lang::txt('Manage'); ?>
				</th>
				<th scope="col" class="priority-3 nowrap">
					<?php echo Html::grid('sort', 'Element', 'element', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-5 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'extension_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $this->items->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$i = 0;
			$folders = $this->items->fieldsByKey('folder');
			foreach ($this->items as $item) :

				$item->loadLanguage(true);

				$ordering   = ($listOrder == 'ordering');
				$canEdit    = User::authorise('core.edit', 'com_plugins');
				$canCheckin = User::authorise('core.manage', 'com_checkin') || $item->checked_out==User::get('id') || $item->checked_out==0;
				$canChange  = User::authorise('core.edit.state', 'com_plugins') && $canCheckin;
				?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="center">
						<?php echo Html::grid('id', $i, $item->extension_id); ?>
					</td>
					<td>
						<?php if ($item->checked_out) : ?>
							<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, '', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit) : ?>
							<a href="<?php echo Route::url('index.php?option=com_plugins&task=edit&id=' . (int) $item->extension_id . '&' . Session::getFormToken() . '=1'); ?>">
								<?php echo Lang::txt($item->name); ?>
							</a>
						<?php else : ?>
							<?php echo Lang::txt($item->name); ?>
						<?php endif; ?>
					</td>
					<td class="center">
						<?php echo Html::grid('published', $item->enabled, $i, '', $canChange); ?>
					</td>
					<td class="priority-2 order">
						<?php if ($canChange) : ?>
							<?php if ($saveOrder) :?>
								<?php if ($listDirn == 'asc') : ?>
									<span><?php echo $this->items->pagination->orderUpIcon($i, (@$folders[$i-1] == $item->folder), 'orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->items->pagination->orderDownIcon($i, $this->items->pagination->total, (@$folders[$i+1] == $item->folder), 'orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php elseif ($listDirn == 'desc') : ?>
									<span><?php echo $this->items->pagination->orderUpIcon($i, (@$folders[$i-1] == $item->folder), 'orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
									<span><?php echo $this->items->pagination->orderDownIcon($i, $this->items->pagination->total, (@$folders[$i+1] == $item->folder), 'orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
								<?php endif; ?>
							<?php endif; ?>
							<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled ?> class="text-area-order" />
						<?php else : ?>
							<?php echo $item->ordering; ?>
						<?php endif; ?>
					</td>
					<td class="priority-3 nowrap center">
						<?php if (in_array($item->element, $this->manage)) { ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=manage&plugin=' . $item->element); ?>">
								<span><?php echo Lang::txt('Manage'); ?></span>
							</a>
						<?php } ?>
					</td>
					<td class="priority-3 nowrap center">
						<?php echo $this->escape($item->element); ?>
					</td>
					<td class="priority-4 center">
						<?php echo $this->escape($item->access_level); ?>
					</td>
					<td class="priority-5 center">
						<?php echo (int) $item->extension_id; ?>
					</td>
				</tr>
				<?php
				$i++;
			endforeach;
			?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>