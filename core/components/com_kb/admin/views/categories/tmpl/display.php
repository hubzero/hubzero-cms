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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Kb\Admin\Helpers\Permissions::getActions('category');

Toolbar::title(Lang::txt('COM_KB'), 'kb.png');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
	Toolbar::spacer();
}
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
	Toolbar::spacer();
}
if ($canDo->get('core.create'))
{
	Toolbar::addNew();
}
if ($canDo->get('core.edit'))
{
	Toolbar::editList();
}
if ($canDo->get('core.delete'))
{
	Toolbar::deleteList();
}
Toolbar::spacer();
Toolbar::help('categories');
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('JSEARCH_FILTER'); ?>" />

			<input type="submit" value="<?php echo Lang::txt('COM_KB_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');$('#filter-state').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
			<select name="access" id="filter-access" onchange="this.form.submit()">
				<option value=""><?php echo Lang::txt('JOPTION_SELECT_ACCESS');?></option>
				<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->filters['access']); ?>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KB_TITLE', 'a.title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo Html::grid('sort', 'COM_KB_PUBLISHED', 'a.state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo Html::grid('sort', 'COM_KB_ACCESS', 'a.access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_KB_SUB_CATEGORIES', 'categories', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo Html::grid('sort', 'COM_KB_QUESTIONS', 'articles', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6"><?php
				// Initiate paging
				echo $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
//for ($i=0, $n=count($this->rows); $i < $n; $i++)
$i = 0;
foreach ($this->rows as $row)
{
	//$row =& $this->rows[$i];
	switch ($row->get('state'))
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = Lang::txt('COM_KB_PUBLISHED');
			break;
		case 2:
			$class = 'expire';
			$task = 'publish';
			$alt = Lang::txt('COM_KB_TRASHED');
			break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = Lang::txt('COM_KB_UNPUBLISHED');
			break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
					<?php if ($canDo->get('core.edit')) { ?>
						<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_KB_EDIT_CATEGORY'); ?>">
							<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
						</a>
					<?php } else { ?>
						<span class="glyph category">
							<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-3">
					<?php if ($canDo->get('core.edit.state')) { ?>
						<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_KB_SET_TASK', $task);?>">
							<span><?php echo $alt; ?></span>
						</a>
					<?php } else { ?>
						<span class="state <?php echo $class; ?>">
							<span><?php echo $alt; ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-4">
					<span class="access">
						<?php echo $row->get('groupname'); ?>
					</span>
				</td>
				<td>
					<?php if ($row->get('categories', 0) > 0) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&id=' . $row->get('id')); ?>" title="<?php echo Lang::txt('COM_KB_VIEW_CATEGORIES_FOR_CATEGORY'); ?>">
							<span><?php echo $row->get('categories', 0); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $row->get('categories', 0); ?></span>
						</span>
					<?php } ?>
				</td>
				<td class="priority-2">
					<?php if ($row->get('articles', 0) > 0) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=articles&' . (isset($this->filters['section']) && $this->filters['section'] ? 'category=' . $row->get('id') . '&section=' . $this->filters['section'] : 'section=' . $row->get('id') . '&category=-1')); ?>" title="<?php echo Lang::txt('COM_KB_VIEW_ARTICLES_FOR_CATEGORY'); ?>">
							<span><?php echo $row->get('articles', 0) . ' ' . Lang::txt('COM_KB_ARTICLES'); ?></span>
						</a>
					<?php } else { ?>
						<span>
							<span><?php echo $row->get('articles', 0); ?></span>
						</span>
					<?php } ?>
				</td>
			</tr>
<?php
	$i++;
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" autocomplete="off" />
	<input type="hidden" name="cid" value="<?php echo $this->filters['section']; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>