<?php
/**
 * @package		Joomla.Administrator
 * @subpackage	com_content
 * @copyright	Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_HZEXEC_') or die();

Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$userId    = User::get('id');
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$saveOrder = $listOrder == 'a.ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_content&view=articles');?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_CONTENT_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_published" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Html::grid('publishedOptions'), 'value', 'text', $this->state->get('filter.published'), true);?>
			</select>

			<select name="filter_category_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_CATEGORY');?></option>
				<?php echo Html::select('options', Html::category('options', 'com_content'), 'value', 'text', $this->state->get('filter.category_id'));?>
			</select>

			<select name="filter_level" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_MAX_LEVELS');?></option>
				<?php echo Html::select('options', $this->f_levels, 'value', 'text', $this->state->get('filter.level'));?>
			</select>

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->state->get('filter.access'));?>
			</select>

			<select name="filter_author_id" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_AUTHOR');?></option>
				<?php echo Html::select('options', $this->authors, 'value', 'text', $this->state->get('filter.author_id'));?>
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
				<th>
					<?php echo Html::grid('sort', 'JSTATUS', 'a.state', $listDirn, $listOrder); ?>
				</th>
				<?php /*<th class="priority-4">
					<?php echo Html::grid('sort', 'JFEATURED', 'a.featured', $listDirn, $listOrder, NULL, 'desc'); ?>
				</th>*/ ?>
				<th class="priority-2">
					<?php echo Html::grid('sort', 'JCATEGORY', 'category_title', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-3">
					<?php echo Html::grid('sort',  'JGRID_HEADING_ORDERING', 'a.ordering', $listDirn, $listOrder); ?>
					<?php if ($saveOrder) :?>
						<?php echo Html::grid('order',  $this->items, 'filesave.png', 'articles.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access_level', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_CREATED_BY', 'a.created_by', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5">
					<?php echo Html::grid('sort', 'JDATE', 'a.created', $listDirn, $listOrder); ?>
				</th>
				<!-- [!] HUBZERO - (zooley) Removing hit counter as it can contribute to performance issues. Need a better way of doing this.
				<th>
					<?php echo Html::grid('sort', 'JGLOBAL_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th> -->
				<th class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language', $listDirn, $listOrder); ?>
				</th>
				<th class="priority-5 nowrap">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
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
			$item->max_ordering = 0; //??
			$ordering   = ($listOrder == 'a.ordering');
			$canCreate  = User::authorise('core.create',     'com_content.category.'.$item->catid);
			$canEdit    = User::authorise('core.edit',       'com_content.article.'.$item->id);
			$canCheckin = User::authorise('core.manage',     'com_checkin') || $item->checked_out == $userId || $item->checked_out == 0;
			$canEditOwn = User::authorise('core.edit.own',   'com_content.article.'.$item->id) && $item->created_by == $userId;
			$canChange  = User::authorise('core.edit.state', 'com_content.article.'.$item->id) && $canCheckin;
			?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($item->checked_out) : ?>
						<?php echo Html::grid('checkedout', $i, $item->editor, $item->checked_out_time, 'articles.', $canCheckin); ?>
					<?php endif; ?>
					<?php if ($canEdit || $canEditOwn) : ?>
						<a href="<?php echo Route::url('index.php?option=com_content&task=article.edit&id='.$item->id);?>">
							<?php echo $this->escape($item->title); ?></a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<p class="smallsub">
						<?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->alias));?></p>
				</td>
				<td class="center">
					<?php echo Html::grid('published', $item->state, $i, 'articles.', $canChange, 'cb', $item->publish_up, $item->publish_down); ?>
				</td>
				<?php /*<td class="priority-4 center">
					<?php echo Html::contentadministrator('featured', $item->featured, $i, $canChange); ?>
				</td>*/ ?>
				<td class="priority-2 center">
					<?php echo $this->escape($item->category_title); ?>
				</td>
				<td class="priority-3 order">
					<?php if ($canChange) : ?>
						<?php if ($saveOrder) :?>
							<?php if ($listDirn == 'asc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'articles.orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'articles.orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $this->pagination->orderUpIcon($i, ($item->catid == @$this->items[$i-1]->catid), 'articles.orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $this->pagination->orderDownIcon($i, $this->pagination->total, ($item->catid == @$this->items[$i+1]->catid), 'articles.orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ?  '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering;?>" <?php echo $disabled ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="priority-4 center">
					<?php echo $this->escape($item->access_level); ?>
				</td>
				<td class="priority-6 center">
					<?php if ($item->created_by_alias) : ?>
						<?php echo $this->escape($item->author_name); ?>
						<p class="smallsub"> <?php echo Lang::txt('JGLOBAL_LIST_ALIAS', $this->escape($item->created_by_alias)); ?></p>
					<?php else : ?>
						<?php echo $this->escape($item->author_name); ?>
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
