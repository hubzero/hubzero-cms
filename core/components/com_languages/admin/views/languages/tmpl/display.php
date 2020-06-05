<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canDo = Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_LANGUAGES_TITLE'), 'langmanager');

if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}

if ($canDo->get('core.edit'))
{
	Toolbar::editList();
	Toolbar::divider();
}

if ($canDo->get('core.edit.state'))
{
	if ($this->filters['published'] != 2)
	{
		Toolbar::publishList();
		Toolbar::unpublishList();
	}
}

if ($this->filters['published'] == -2 && $canDo->get('core.delete'))
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
	// Add install languages link to the lang installer component
	Toolbar::appendButton('Link', 'extension', 'COM_LANGUAGES_INSTALL', 'index.php?option=com_installer&view=languages');
	Toolbar::divider();

	Toolbar::preferences($this->option);
	Toolbar::divider();
}

Toolbar::help('languages');

Html::behavior('tooltip');
Html::behavior('multiselect');

$userId    = User::get('id');
$n         = count($this->items);
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', $this->option);
$saveOrder = $listOrder == 'a.ordering';
$pagination = $this->items->pagination;
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_LANGUAGES_SEARCH_IN_TITLE'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Components\Languages\Helpers\Utilities::publishedOptions(), 'value', 'text', $this->filters['published'], true); ?>
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
				<th class="priority-6">
					<?php echo Lang::txt('JGRID_HEADING_ROW_NUMBER'); ?>
				</th>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort', 'COM_LANGUAGES_HEADING_TITLE_NATIVE', 'title_native', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'COM_LANGUAGES_FIELD_LANG_TAG_LABEL', 'lang_code', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'COM_LANGUAGES_FIELD_LANG_CODE_LABEL', 'sef', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'COM_LANGUAGES_HEADING_LANG_IMAGE', 'image', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave', 'saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort', 'COM_LANGUAGES_HOMEPAGE', '', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'lang_id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="12">
					<?php echo $pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$canCreate = User::authorise('core.create', $this->option);
		$canEdit   = User::authorise('core.edit', $this->option);
		$canChange = User::authorise('core.edit.state', $this->option);

		foreach ($this->items as $i => $item) :
			$ordering = ($listOrder == 'a.ordering');
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="priority-6">
					<?php echo $pagination->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo Html::grid('id', $i, $item->lang_id); ?>
				</td>
				<td>
					<span class="editlinktip hasTip" title="<?php echo Lang::txt('JGLOBAL_EDIT_ITEM');?>::<?php echo $this->escape($item->title); ?>">
						<?php if ($canEdit) : ?>
							<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&lang_id=' . (int) $item->lang_id); ?>">
								<?php echo $this->escape($item->title); ?>
							</a>
						<?php else : ?>
							<?php echo $this->escape($item->title); ?>
						<?php endif; ?>
					</span>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($item->title_native); ?>
				</td>
				<td>
					<?php echo $this->escape($item->lang_code); ?>
				</td>
				<td class="priority-6">
					<?php echo $this->escape($item->sef); ?>
				</td>
				<td class="priority-6">
					<?php echo $this->escape($item->image); ?>
				</td>
				<td>
					<?php echo Html::grid('published', $item->published, $i, '', $canChange); ?>
				</td>
				<td class="order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $pagination->orderUpIcon($i, true, 'orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $pagination->orderDownIcon($i, $pagination->total, true, 'orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $pagination->orderUpIcon($i, true, 'orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $pagination->orderDownIcon($i, $pagination->total, true, 'orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled; ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="priority-3">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="priority-4">
					<?php if ($item->home == '1') : ?>
						<?php echo Lang::txt('JYES');?>
					<?php else:?>
						<?php echo Lang::txt('JNO');?>
					<?php endif;?>
				</td>
				<td class="priority-5">
					<?php echo $this->escape($item->lang_id); ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
