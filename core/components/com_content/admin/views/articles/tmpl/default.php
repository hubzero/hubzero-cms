<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canAdmin = User::authorise('core.admin', 'com_content');
$canCreate = User::authorise('core.create', 'com_content');
$canEdit = User::authorise('core.edit', 'com_content');
$canChangeState = User::authorise('core.edit.state', 'com_content');
$canDelete = User::authorise('core.delete', 'com_content');

Html::behavior('multiselect');
Toolbar::title(Lang::txt('COM_CONTENT_ARTICLES_TITLE'), 'content');

if ($canCreate)
{
	Toolbar::addNew();
}
if ($canEdit)
{
	Toolbar::editList();
}
Toolbar::spacer();
if ($canChangeState)
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
	Toolbar::archiveList();
	Toolbar::checkin();
}
if ($canDelete)
{
	Toolbar::deleteList('', 'trash');
}
if ($canAdmin)
{
	Toolbar::spacer();
	Toolbar::preferences($this->option, '550');
}
Toolbar::spacer();
Toolbar::help('articles');

Html::behavior('tooltip');

$userId    = User::get('id');
$listOrder = $this->filters['sort'];
$listDirn  = $this->filters['sort_Dir'];
$saveOrder = $listOrder == 'ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_content&view=articles');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" class="filter" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" class="filter-clear"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->filters['published'], true);?>
			</select>

			<select name="filter_category_id" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo Html::select('options', Html::category('options', 'com_content'), 'value', 'text', $this->filters['category_id']); ?>
			</select>

			<select name="filter_level" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_MAX_LEVELS');?></option>
				<?php echo Html::select('options', $this->f_levels, 'value', 'text', $this->filters['level']); ?>
			</select>

			<select name="filter_access" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>

			<select name="filter_author_id" class="inputbox" class="filter filter-submit">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_AUTHOR');?></option>
				<?php echo Html::select('options', $this->authors, 'value', 'text', $this->filters['author_id']); ?>
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
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th>
					<?php echo Html::grid('sort', 'JSTATUS', 'state', $listDirn, $listOrder); ?>
				</th>
				<?php /*<th class="priority-4">
					<?php echo Html::grid('sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
				</th>*/ ?>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JCATEGORY', 'catid', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_CREATED_BY', 'created_by', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'JDATE', 'created', $listDirn, $listOrder); ?>
				</th>
				<!-- [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_HITS', 'hits', $listDirn, $listOrder); ?>
				</th> -->
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pagination; ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php foreach ($this->items as $i => $item) :
			$item->max_ordering = 0; //??
			$ordering   = ($listOrder == 'ordering');
			$canCreate  = User::authorise('core.create', 'com_content.category.' . $item->catid);
			$canEdit    = User::authorise('core.edit', 'com_content.article.' . $item->id);
			$canCheckin = User::authorise('core.manage', 'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn = User::authorise('core.edit.own', 'com_content.article.' . $item->id) && $item->created_by == $userId;
			$canChange  = User::authorise('core.edit.state', 'com_content.article.' . $item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor->name, $item->checked_out_time, 'articles.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller .'&task=edit&id='.$item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?>
					</p>
				</td>
				<td class="center">
					<?php echo Html::grid('published', $item->get('state'), $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<?php /*<td class="priority-4 center">
					<?php echo Html::contentadministrator('featured', $item->featured, $i, $canChange); ?>
				</td>*/ ?>
				<td class="priority-2 center">
					<?php echo $this->escape($item->category->title); ?>
				</td>
				<td class="priority-3 order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'articles.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'articles.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[<?php echo $item->get('catid', 0);?>][<?php echo $item->get('id', 0);?>]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="priority-4 center">
					<?php echo $this->escape($item->accessLevel->title); ?>
				</td>
				<td class="priority-6 center">
					<?php if ($item->created_by_alias) : ?>
						<?php echo $this->escape($item->author_name); ?>
						<p class="smallsub"> <?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->created_by_alias)); ?></p>
					<?php else : ?>
						<?php echo $this->escape($item->author->name); ?>
					<?php endif; ?>
				</td>
				<td class="priority-5 center nowrap">
					<?php echo Date::of($item->created)->toLocal(Lang::txt('DATE_FORMAT_LC4')); ?>
				</td>
				<!-- [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
				<td class="center">
					<?php echo (int) $item->hits; ?>
				</td> -->
				<td class="priority-6 center">
					<?php if ($item->language=='*'):?>
						<?php echo Lang::txt('JALL', 'language'); ?>
					<?php else:?>
						<?php echo $item->language_title ? $this->escape($item->language_title) : Lang::txt('JUNDEFINED'); ?>
					<?php endif;?>
				</td>
				<td class="priority-5 center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php //Load the batch processing form. ?>
	<?php if (User::authorise('core.create', 'com_content') && User::authorise('core.edit', 'com_content') && User::authorise('core.edit.state', 'com_content')) : ?>
		<?php echo $this->loadTemplate('batch'); ?>
	<?php endif;?>

	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
