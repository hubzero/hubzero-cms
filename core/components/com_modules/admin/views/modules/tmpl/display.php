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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = Components\Modules\Helpers\Modules::getActions();

Toolbar::title(Lang::txt('COM_MODULES_MANAGER_MODULES'), 'module.png');

if ($canDo->get('core.create'))
{
	//Toolbar::addNew('module.add');
	Toolbar::appendButton('Popup', 'new', 'JTOOLBAR_NEW', 'index.php?option=com_modules&amp;task=select&amp;tmpl=component', 850, 400);
}

if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}

if ($canDo->get('core.create'))
{
	Toolbar::custom('duplicate', 'copy.png', 'copy_f2.png', 'JTOOLBAR_DUPLICATE', true);
}

if ($canDo->get('core.edit.state'))
{
	Toolbar::divider();
	Toolbar::publish();
	Toolbar::unpublish();
	Toolbar::divider();
	Toolbar::checkin();
}

if ($this->filters['state'] == -2 && $canDo->get('core.delete'))
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
	Toolbar::preferences('com_modules');
	Toolbar::divider();
}
Toolbar::help('modules');


Html::behavior('tooltip');
Html::behavior('multiselect');

$client    = $this->filters['client_id'] ? 'administrator' : 'site';
$listOrder = $this->escape($this->filters['sort']);
$listDirn  = $this->escape($this->filters['sort_Dir']);
$canOrder  = User::authorise('core.edit.state', 'com_modules');
$saveOrder = $listOrder == 'ordering';
?>
<form action="<?php echo Route::url('index.php?option=com_modules'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_MODULES_MODULES_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="filter-select fltrt">
			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<?php echo Html::select('options', Components\Modules\Helpers\Modules::getClientOptions(), 'value', 'text', $this->filters['client_id']); ?>
			</select>

			<select name="filter_state" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_PUBLISHED');?></option>
				<?php echo Html::select('options', Components\Modules\Helpers\Modules::getStateOptions(), 'value', 'text', $this->filters['state']); ?>
			</select>

			<select name="filter_position" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MODULES_OPTION_SELECT_POSITION');?></option>
				<?php echo Html::select('options', Components\Modules\Helpers\Modules::getPositions($this->filters['client_id']), 'value', 'text', $this->filters['position']); ?>
			</select>

			<select name="filter_module" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('COM_MODULES_OPTION_SELECT_MODULE');?></option>
				<?php echo Html::select('options', Components\Modules\Helpers\Modules::getModules($this->filters['client_id']), 'value', 'text', $this->filters['module']); ?>
			</select>

			<select name="filter_access" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>

			<select name="filter_language" class="inputbox" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_LANGUAGE');?></option>
				<?php echo Html::select('options', Html::contentlanguage('existing', true, true), 'value', 'text', $this->filters['language']); ?>
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
					<?php echo Html::grid('sort', 'JGLOBAL_TITLE', 'title', $listDirn, $listOrder); ?>
				</th>
				<th scope="col">
					<?php echo Html::grid('sort', 'JSTATUS', 'published', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-2 left">
					<?php echo Html::grid('sort', 'COM_MODULES_HEADING_POSITION', 'position', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ORDERING', 'ordering', $listDirn, $listOrder); ?>
					<?php if ($canOrder && $saveOrder) :?>
						<?php echo Html::grid('order', $this->items, 'filesave.png', 'saveorder'); ?>
					<?php endif; ?>
				</th>
				<th scope="col" class="priority-3 left">
					<?php echo Html::grid('sort', 'COM_MODULES_HEADING_MODULE', 'name', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort', 'COM_MODULES_HEADING_PAGES', 'pages', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ACCESS', 'access', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-5">
					<?php echo Html::grid('sort', 'JGRID_HEADING_LANGUAGE', 'language_title', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="priority-6">
					<?php echo Html::grid('sort', 'JGRID_HEADING_ID', 'id', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php //echo $this->items->pagination; ?>
					<?php 
				// Initiate paging
				$pagination = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				echo $pagination;
			?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$positions = $this->items->fieldsByKey('position');
		foreach ($this->items as $item) :
			$ordering   = ($listOrder == 'ordering');
			$canCreate  = User::authorise('core.create', 'com_modules');
			$canEdit    = User::authorise('core.edit', 'com_modules');
			$canCheckin = User::authorise('core.manage', 'com_checkin') || $item->checked_out == User::get('id')|| $item->checked_out==0;
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
						<a href="<?php echo Route::url('index.php?option=com_modules&task=edit&id=' . (int) $item->id); ?>">
							<?php echo $this->escape($item->title); ?>
						</a>
					<?php else : ?>
						<?php echo $this->escape($item->title); ?>
					<?php endif; ?>
					<?php if (!empty($item->note)) : ?>
						<p class="smallsub">
							<?php echo Lang::txt('JGLOBAL_LIST_NOTE', $this->escape($item->note)); ?>
						</p>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo Components\Modules\Helpers\Modules::state($item->published, $i, $canChange, 'cb'); ?>
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
								<span><?php echo $pagination->orderUpIcon($i, (@$positions[$i-1] == $item->position), 'orderup', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $pagination->orderDownIcon($i, $pagination->total, (@$positions[$i+1] == $item->position), 'orderdown', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php elseif ($listDirn == 'desc') : ?>
								<span><?php echo $pagination->orderUpIcon($i, (@$positions[$i-1] == $item->position), 'orderdown', 'JLIB_HTML_MOVE_UP', $ordering); ?></span>
								<span><?php echo $pagination->orderDownIcon($i, $pagination->total, (@$positions[$i+1] == $item->position), 'orderup', 'JLIB_HTML_MOVE_DOWN', $ordering); ?></span>
							<?php endif; ?>
						<?php endif; ?>
						<?php $disabled = $saveOrder ? '' : 'disabled="disabled"'; ?>
						<input type="text" name="order[]" size="5" value="<?php echo $item->ordering; ?>" <?php echo $disabled; ?> class="text-area-order" />
					<?php else : ?>
						<?php echo $item->ordering; ?>
					<?php endif; ?>
				</td>
				<td class="priority-3 left">
					<?php echo $item->name; ?>
				</td>
				<td class="priority-4 center">
					<?php
					$pages = $item->pages;
					if (is_null($item->pages))
					{
						$pages = Lang::txt('JNONE');
					}
					elseif ($item->pages < 0)
					{
						$pages = Lang::txt('COM_MODULES_ASSIGNED_VARIES_EXCEPT');
					}
					elseif ($item->pages > 0)
					{
						$pages = Lang::txt('COM_MODULES_ASSIGNED_VARIES_ONLY');
					}
					else
					{
						$pages = Lang::txt('JALL');
					}
					echo $pages;
					?>
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
			<?php
				$i++;
			endforeach;
			?>
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
