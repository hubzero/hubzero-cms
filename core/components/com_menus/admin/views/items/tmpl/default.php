<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_menus
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
Html::addIncludePath(JPATH_COMPONENT . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$app       = JFactory::getApplication();
$userId    = User::get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$ordering  = ($listOrder == 'a.lft');
$canOrder  = User::authorise('core.edit.state', 'com_menus');
$saveOrder = ($listOrder == 'a.lft' && $listDirn == 'asc');
?>
<?php //Set up the filter bar. ?>
<form action="<?php echo Route::url('index.php?option=com_menus&view=items');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_MENUS_ITEMS_SEARCH_FILTER'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="menutype" class="inputbox" onchange="this.form.submit()">
				<?php echo Html::select('options', JHtml::_('menu.menus'), 'value', 'text', $this->state->get('filter.menutype'));?>
			</select>

			<select name="filter_level" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MENUS_OPTION_SELECT_LEVEL');?></option>
				<?php echo Html::select('options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'));?>
			</select>

			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions', array('archived' => false)), 'value', 'text', $this->state->get('filter.published'), true);?>
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

<?php //Set up the grid heading. ?>
	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th class="title">
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'a.lft', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order',  $this->items, 'filesave.png', 'items.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Lang::txt('JGRID_HEADING_MENU_ITEM_TYPE'); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'COM_MENUS_HEADING_HOME', 'a.home', $listDirn, $listOrder); ?>
				</th>
				<?php
				$assoc = isset($app->menu_associations) ? $app->menu_associations : 0;
				if ($assoc):
				?>
				<th>
					<?php echo Html::grid('sort', 'COM_MENUS_HEADING_ASSOCIATION', 'association', $listDirn, $listOrder); ?>
				</th>
				<?php endif;?>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th class="nowrap priority-6">
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
		<?php // Grid layout ?>
		<tbody>
		<?php
		$originalOrders = array();
		foreach ($this->items as $i => $item) :
			$orderkey = array_search($item->id, $this->ordering[$item->parent_id]);
			$canCreate  = User::authorise('core.create',     'com_menus');
			$canEdit    = User::authorise('core.edit',       'com_menus');
			$canCheckin = User::authorise('core.manage',     'com_checkin') || $item->checked_out==User::get('id')|| $item->checked_out==0;
			$canChange  = User::authorise('core.edit.state', 'com_menus') && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">|&mdash;</span>', $item->level-1) ?>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, 'items.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=com_menus&task=item.edit&id='.(int) $item->id);?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub" title="<?php echo $this->escape($item->path);?>">
						<?php echo str_repeat('<span class="gtr">|&mdash;</span>', $item->level-1) ?>
						<?php if ($item->type !='url') : ?>
							<?php if (empty($item->note)) : ?>
								<?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
							<?php else : ?>
								<?php echo Lang::txt('JGLOBAL_LIST_ALIAS_NOTE', $this->escape($item->alias), $this->escape($item->note));?>
							<?php endif; ?>
						<?php elseif ($item->type =='url' && $item->note) : ?>
							<?php echo Lang::txt('JGLOBAL_LIST_NOTE', $this->escape($item->note));?>
						<?php endif; ?>
					</p>
				</td>
				<td class="center priority-3">
					<?php echo Html::menus('state', $item->published, $i, $canChange, 'cb'); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) : ?>
							<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'items.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
							<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'items.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
						<?php $originalOrders[] = $orderkey + 1; ?>
					<?php else : ?>
						<?php echo $orderkey + 1;?>
					<?php endif; ?>
				</td>
				<td class="center priority-4">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="nowrap priority-5">
					<span title="<?php echo isset($item->item_type_desc) ? htmlspecialchars($this->escape($item->item_type_desc), ENT_COMPAT, 'UTF-8') : ''; ?>">
						<?php echo $this->escape($item->item_type); ?>
					</span>
				</td>
				<td class="center priority-2">
					<?php if ($item->type == 'component') : ?>
						<?php if ($item->language=='*' || $item->home=='0'):?>
							<?php echo Html::grid('isdefault', $item->home, $i, 'items.', ($item->language != '*' || !$item->home) && $canChange);?>
						<?php elseif ($canChange):?>
							<a href="<?php echo Route::url('index.php?option=com_menus&task=items.unsetDefault&cid[]='.$item->id.'&'.Session::getFormToken().'=1');?>">
								<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>Lang::txt('COM_MENUS_GRID_UNSET_LANGUAGE', $item->language_title)), true);?>
							</a>
						<?php else:?>
							<?php echo Html::asset('image', 'mod_languages/'.$item->image.'.gif', $item->language_title, array('title'=>$item->language_title), true);?>
						<?php endif;?>
					<?php endif; ?>
				</td>
				<?php
				$assoc = isset($app->menu_associations) ? $app->menu_associations : 0;
				if ($assoc):
				?>
				<td class="center">
					<?php if ($item->association):?>
						<?php echo Html::menus('association', $item->id);?>
					<?php endif;?>
				</td>
				<?php endif;?>
				<td class="center priority-6">
					<?php if ($item->language==''):?>
						<?php echo Lang::txt('JDEFAULT'); ?>
					<?php elseif ($item->language=='*'):?>
						<?php echo Lang::txt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="center priority-6">
					<span title="<?php echo sprintf('%d-%d', $item->lft, $item->rgt);?>">
						<?php echo (int) $item->id; ?>
					</span>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>
	<?php //Load the batch processing form.is user is allowed ?>
	<?php if (User::authorise('core.create', 'com_menus') && User::authorise('core.edit', 'com_menus') && User::authorise('core.edit.state', 'com_menus')) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<input type="hidden" name="original_order_values" value="<?php echo implode($originalOrders, ','); ?>" />
	<?php echo Html::input('token'); ?>
</form>
