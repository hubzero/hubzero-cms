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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

// Include the component HTML helpers.
//Html::addIncludePath(JPATH_COMPONENT.'/helpers/html');
Html::behavior('tooltip');
Html::behavior('multiselect');

//$state = $this->get('State');
$canDo = \Components\Plugins\Helpers\Plugins::getActions();

Toolbar::title(Lang::txt('COM_PLUGINS_MANAGER_PLUGINS'), 'plugin');

if ($canDo->get('core.edit'))
{
	Toolbar::editList('edit');
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::publish('publish', 'JTOOLBAR_ENABLE', true);
	Toolbar::unpublish('unpublish', 'JTOOLBAR_DISABLE', true);
	Toolbar::divider();
	Toolbar::checkin('checkin');
}
if ($canDo->get('core.admin'))
{
	Toolbar::divider();
	Toolbar::preferences('com_plugins');
}
Toolbar::divider();
Toolbar::help('plugins');

$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', 'com_plugins');
$saveOrder = $listOrder == 'ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_plugins'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_PLUGINS_SEARCH_IN_TITLE'); ?>" />
			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>

		<div class="filter-select fltrt">
			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', \Components\Plugins\Helpers\Plugins::stateOptions(), 'value', 'text', $this->filters['state'], true);?>
			</select>

			<select name="filter_folder" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_PLUGINS_OPTION_FOLDER');?></option>
				<?php echo Html::select('options', \Components\Plugins\Helpers\Plugins::folderOptions(), 'value', 'text', $this->filters['folder']);?>
			</select>

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>
		</div>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th>
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th scope="col" class="title">
					<?php echo Html::grid('sort', 'COM_PLUGINS_NAME_HEADING', 'name', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JSTATUS', 'enabled', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order',  $this->items, 'filesave.png', 'plugins.saveorder'); ?>
					<?php endif; ?>
				</th>
				<th scope="col" class="priority-3 nowrap">
					<?php echo Html::grid('sort', 'COM_PLUGINS_FOLDER_HEADING', 'folder', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-3 nowrap">
					<?php echo Html::grid('sort', 'COM_PLUGINS_ELEMENT_HEADING', 'element', $listDirn, $listOrder); ?>
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
						<?php echo $this->escape($item->folder); ?>
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

	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />
	<?php echo Html::input('token'); ?>
</form>
