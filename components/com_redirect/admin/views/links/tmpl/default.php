<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_JEXEC') or die;

$canDo = \Components\Redirect\Helpers\Redirect::getActions();

Toolbar::title(Lang::txt('COM_REDIRECT_MANAGER_LINKS'), 'redirect');
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
	if ($this->state->get('filter.state') != 2)
	{
		Toolbar::divider();
		Toolbar::publish('publish', 'JTOOLBAR_ENABLE', true);
		Toolbar::unpublish('unpublish', 'JTOOLBAR_DISABLE', true);
	}
	if ($this->state->get('filter.state') != -1 )
	{
		Toolbar::divider();
		if ($this->state->get('filter.state') != 2)
		{
			Toolbar::archiveList('archive');
		}
		elseif ($this->state->get('filter.state') == 2)
		{
			Toolbar::unarchiveList('publish', 'JTOOLBAR_UNARCHIVE');
		}
	}
}
if ($this->state->get('filter.state') == -2 && $canDo->get('core.delete'))
{
	Toolbar::deleteList('', 'delete', 'JTOOLBAR_EMPTY_TRASH');
	Toolbar::divider();
}
elseif ($canDo->get('core.edit.state'))
{
	Toolbar::trash('trash');
	Toolbar::divider();
}
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_redirect');
	Toolbar::divider();
}
Toolbar::help('links');

// Include the component HTML helpers.
Html::addIncludePath(dirname(JPATH_COMPONENT) . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&view=links'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_REDIRECT_SEARCH_LINKS'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', \Components\Redirect\Helpers\Redirect::publishedOptions(), 'value', 'text', $this->state->get('filter.state'), true);?>
			</select>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th scope="col" class="title">
					<?php echo $this->grid('sort', 'COM_REDIRECT_HEADING_OLD_URL', 'a.old_url', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_REDIRECT_HEADING_NEW_URL', 'a.new_url', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo $this->grid('sort', 'COM_REDIRECT_HEADING_REFERRER', 'a.referer', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo $this->grid('sort', 'COM_REDIRECT_HEADING_CREATED_DATE', 'a.created_date', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo $this->grid('sort', 'JSTATUS', 'a.published', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo $this->grid('sort', 'COM_REDIRECT_HEADING_HITS', 'a.hits', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-5 nowrap">
					<?php echo $this->grid('sort', 'JGRID_HEADING_ID', 'a.id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
			<tr>
				<td colspan="8">
					<p class="info">
						<?php if ($this->enabled) : ?>
							<span class="enabled"><?php echo Lang::txt('COM_REDIRECT_PLUGIN_ENABLED'); ?></span>
						<?php else : ?>
							<span class="disabled"><?php echo Lang::txt('COM_REDIRECT_PLUGIN_DISABLED'); ?></span>
						<?php endif; ?>
					</p>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$canCreate = User::authorise('core.create', $this->option);
		$canEdit   = User::authorise('core.edit', $this->option);
		$canChange = User::authorise('core.edit.state', $this->option);
		foreach ($this->items as $i => $item) :
		?>
			<tr class="row<?php echo $i % 2; ?>">
				<td class="center">
					<?php echo Html::grid('id', $i, $item->id); ?>
				</td>
				<td>
					<?php if ($canEdit) : ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=edit&id=' . $item->id);?>" title="<?php echo $this->escape($item->old_url); ?>">
							<?php echo $this->escape(str_replace(Request::root(), '', $item->old_url)); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape(str_replace(Request::root(), '', $item->old_url)); ?>
					<?php endif; ?>
				</td>
				<td>
					<?php echo $this->escape($item->new_url); ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($item->referer); ?>
				</td>
				<td class="priority-5 center">
					<?php echo Date::of($item->created_date)->toLocal(Lang::txt('DATE_FORMAT_LC4')); ?>
				</td>
				<td class="priority-2 center">
					<?php echo Html::redirect('published', $item->published, $i); ?>
				</td>
				<td class="priority-3 center">
					<?php echo (int) $item->hits; ?>
				</td>
				<td class="priority-5 center">
					<?php echo (int) $item->id; ?>
				</td>
			</tr>
			<?php endforeach; ?>
		</tbody>
	</table>

	<?php if (!empty($this->items)) : ?>
		<?php echo $this->loadTemplate('addform'); ?>
	<?php endif; ?>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo Html::input('token'); ?>
</form>
