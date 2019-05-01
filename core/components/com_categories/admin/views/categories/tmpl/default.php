<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//$canAdmin = User::authorise('core.admin', 'com_categories');
//$canCreate = User::authorise('core.create', 'com_categories');
//$canEdit = User::authorise('core.edit', 'com_categories');
//$canChangeState = User::authorise('core.edit.state', 'com_categories');
//$canDelete = User::authorise('core.delete', 'com_categories');

$userId    = User::get('id');
$extension = $this->filters['extension'];
$listOrder = $this->filters['sort'];
$listDirn  = $this->filters['sort_Dir'];
$ordering  = ($listOrder == 'lft');
$saveOrder = $listOrder == 'lft';

Toolbar::title(Lang::txt('COM_CATEGORIES_CATEGORIES_TITLE', Lang::txt($extension)), 'categories');
if ($this->canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($this->canDo->get('core.edit'))
{
	Toolbar::editList();
}
Toolbar::spacer();
if ($this->canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
	Toolbar::archiveList();
	Toolbar::checkin();
}
if ($this->canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'trash');
}
if ($this->canDo->get('core.admin'))
{
	Toolbar::spacer();
	Toolbar::preferences($this->filters['extension'], '550');
}
Toolbar::spacer();
Toolbar::help('categories');

Html::addIncludePath(PATH_COMPONENT . '/helpers/html');
Html::behavior('multiselect');
Html::behavior('tooltip');

$this->js();
?>
<form action="<?php echo Route::url('index.php?option=com_categories&view=categories');?>" method="post" name="adminForm" id="adminForm">

	<fieldset id="filter-bar">
		<div class="grid">
			<div class="filter-search span4">
				<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
				<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CATEGORIES_ITEMS_SEARCH_FILTER'); ?>" />
				<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
				<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
			</div>

			<div class="filter-select span8 align-right">
				<select name="filter_level" class="inputbox" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_MAX_LEVELS');?></option>
					<?php echo Html::select('options', $this->f_levels, 'value', 'text', $this->filters['level']);?>
				</select>

				<select name="filter_published" class="inputbox" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
					<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->filters['published'], true);?>
				</select>

				<select name="filter_access" class="inputbox" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
					<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']);?>
				</select>

				<select name="filter_language" class="inputbox" class="filter filter-submit">
					<option value=""><?php echo Lang::txt('JOPTION_SELECT_LANGUAGE');?></option>
					<?php echo Html::select('options', Html::contentlanguage('existing', true, true), 'value', 'text', $this->filters['language']);?>
				</select>
			</div>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'lft', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'categories.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
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
				$canEdit    = User::authorise('core.edit', $extension.'.category.'.$item->id);
				$canCheckin = User::authorise('core.admin', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
				$canEditOwn = User::authorise('core.edit.own', $extension.'.category.'.$item->id) && $item->created_user_id == $userId;
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
							<?php echo Html::grid('checkedout', $i, $item->editor->name, $item->checked_out_time, 'categories.', $canCheckin); ?>
						<?php endif; ?>
						<?php if ($canEdit || $canEditOwn) : ?>
							<a href="<?php echo Route::url('index.php?option=com_categories&task=category.edit&id='.$item->id.'&extension='.$extension);?>">
								<?php echo $this->escape($item->title); ?>
							</a>
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
							<?php endif; ?>
						</p>
					</td>
					<td class="priority-2 center">
						<?php echo Html::grid('published', $item->get('published'), $i, 'categories.', $canChange);?>
					</td>
					<td class="priority-2 order">
						<?php if ($canChange) : ?>
							<?php if ($saveOrder) : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, isset($this->ordering[$item->parent_id][$orderkey - 1]), 'categories.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, isset($this->ordering[$item->parent_id][$orderkey + 1]), 'categories.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
							<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
							<input type="text" name="order[<?php echo $item->parent_id;?>][<?php echo $item->id;?>]" size="5" value="<?php echo $orderkey + 1;?>" <?php echo $disabled ?> class="text-area-order" />
							<?php $originalOrders[] = $orderkey + 1; ?>
						<?php else : ?>
							<?php echo $orderkey + 1;?>
						<?php endif; ?>
					</td>
					<td class="priority-3 center">
						<?php echo $this->escape($item->level); ?>
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
							<?php echo (int) $item->id; ?>
						</span>
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
