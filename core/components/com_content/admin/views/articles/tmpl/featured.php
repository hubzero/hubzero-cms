<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

include_once Component::path($this->option) . '/admin/helpers/permissions.php';
$canDo = Components\Content\Admin\Helpers\Permissions::getActions($this->filters['category_id']);

Toolbar::title(Lang::txt('COM_CONTENT_FEATURED_TITLE'), 'featured');

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
	Toolbar::custom('featured.delete', 'remove', 'remove_f2', 'JTOOLBAR_REMOVE', true);
}

if ($this->filters['published'] == -2 && $canDo->get('core.delete'))
{
	Toolbar::deleteList();
	Toolbar::divider();
}
elseif ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::trash();
}

if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_content');
	Toolbar::divider();
}
Toolbar::help('featured');

Html::addIncludePath(PATH_COMPONENT . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$listOrder = $this->filters['sort'];
$listDirn  = $this->filters['sort_Dir'];
$canOrder  = User::authorise('core.edit.state', 'com_content.article');
$saveOrder = $listOrder == 'fp.ordering';
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&task=featured');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->filters['published'], true); ?>
			</select>

			<select name="filter_access" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>

			<select name="filter_language" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo Html::select('options', Html::contentlanguage('existing', true, true), 'value', 'text', $this->filters['language']); ?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" class="checkbox-toggle toggle-all" />
				</th>
				<th class="title">
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'a.title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JCATEGORY', 'a.catid', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'fp.ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'featured.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'a.access', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<!-- [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
				<th width="5%">
					<?php echo Html::grid('sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th> -->
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'a.language', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5 nowrap">
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
		<?php
		foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??
			$ordering   = ($listOrder == 'fp.ordering');
			$assetId    = 'com_content.article.' . $item->id;
			$canCreate  = User::authorise('core.create', 'com_content.category.' . $item->catid);
			$canEdit    = User::authorise('core.edit', 'com_content.article.' . $item->id);
			$canCheckin = User::authorise('core.manage', 'com_checkin') || $item->checked_out == User::get('id') || $item->checked_out == 0;
			$canChange  = User::authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, 'featured.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit) : ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&return=featured&id=' . $item->id);?>">
						<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias)); ?></p>
				</td>
				<td class="center">
					<?php echo Html::grid('published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<td class="center priority-2">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="order priority-3">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'featured.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'featured.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, true, 'featured.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, true, 'featured.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="center priority-4">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="center priority-6">
					<?php if ($item->created_by_alias) : ?>
						<?php echo $this->escape($item->author_name); ?>
						<p class="smallsub"> <?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->created_by_alias)); ?></p>
					<?php else : ?>
						<?php echo $this->escape($item->author_name); ?>
					<?php endif; ?>
				</td>
				<td class="center nowrap priority-5">
					<?php echo Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')); ?>
				</td>
				<?php /* [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
				<td class="center">
					<?php echo (int) $item->hits; ?>
				</td> */ ?>
				<td class="center priority6">
					<?php if ($item->language == '*'): ?>
						<?php echo Lang::txt('JALL', 'language'); ?>
					<?php else: ?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
					<?php endif; ?>
				</td>
				<td class="center priority-5">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="featured" autocomplete="off" />
	<input type="hidden" name="featured" value="1" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
