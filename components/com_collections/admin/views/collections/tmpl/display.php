<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$canDo = \Components\Collections\Helpers\Permissions::getActions('collection');

Toolbar::title(Lang::txt('COM_COLLECTIONS'), 'collection.png');
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
Toolbar::help('collections');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="col width-50 fltlft">
			<label for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER'); ?>:</label>
			<input type="text" name="search" id="filter_search" value="<?php echo $this->escape($this->filters['search']); ?>" placeholder="<?php echo Lang::txt('COM_COLLECTIONS_FILTER_SEARCH_PLACEHOLDER'); ?>" />

			<input type="submit" value="<?php echo Lang::txt('COM_COLLECTIONS_GO'); ?>" />
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
		<div class="col width-50 fltrt">
			<label for="filter-state"><?php echo Lang::txt('COM_COLLECTIONS_FIELD_STATE'); ?>:</label>
			<select name="state" id="filter-state" onchange="this.form.submit();">
				<option value="-1"<?php if ($this->filters['state'] == -1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_COLLECTIONS_ALL_STATES'); ?></option>
				<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
				<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
				<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('JTRASHED'); ?></option>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->total(); ?>);" /></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-2"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_STATE', 'state', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-4"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_ACCESS', 'access', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col" class="priority-3"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_OWNER', 'object_type', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo JHTML::_('grid.sort', 'COM_COLLECTIONS_COL_POSTS', 'posts', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="6">
					<?php
					jimport('joomla.html.pagination');
					$pageNav = new JPagination(
						$this->total,
						$this->filters['start'],
						$this->filters['limit']
					);
					echo $pageNav->getListFooter();
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
<?php
$k = 0;
$i = 0;
foreach ($this->rows as $row)
{
	//$row =& $this->rows[$i];
	switch ($row->get('state'))
	{
		case 1:
			$class = 'publish';
			$task = 'unpublish';
			$alt = Lang::txt('JPUBLISHED');
			break;
		case 2:
			$class = 'trash';
			$task = 'publish';
			$alt = Lang::txt('JTRASHED');
			break;
		case 0:
			$class = 'unpublish';
			$task = 'publish';
			$alt = Lang::txt('JUNPUBLISHED');
			break;
	}

	switch ($row->get('access', 0))
	{
		case 0:
			$color_access = 'public';
			$task_access = 'accessregistered';
			$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_PUBLIC'));
		break;
		case 1:
			$color_access = 'registered';
			//$task_access = 'accessspecial';
			$task_access = 'accessprivate';
			$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_REGISTERED'));
		break;
		/*case 2:
			$color_access = 'special';
			$task_access = 'accessprivate';
			$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_SPECIAL'));
		break;*/
		case 4:
			$color_access = 'private';
			$task_access = 'accesspublic';
			$row->set('groupname', Lang::txt('COM_COLLECTIONS_ACCESS_PRIVATE'));
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->get('id'); ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td>
				<?php if ($canDo->get('core.edit')) { ?>
					<a class="glyph category" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->get('id')); ?>">
						<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
					</a>
				<?php } else { ?>
					<span class="glyph category">
						<span><?php echo $this->escape(stripslashes($row->get('title'))); ?></span>
					</span>
				<?php } ?>
				</td>
				<td class="priority-2">
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a class="state <?php echo $class; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->get('id') . '&' . JUtility::getToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_SET_TASK', $task);?>">
						<span><?php echo $alt; ?></span>
					</a>
				<?php } else { ?>
					<span class="state <?php echo $class; ?>">
						<span><?php echo $alt; ?></span>
					</span>
				<?php } ?>
				</td>
				<td class="priority-4">
				<?php if ($canDo->get('core.edit.state')) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task_access . '&id=' . $row->get('id') . '&' . JUtility::getToken() . '=1'); ?>" class="access <?php echo $color_access; ?>" title="<?php echo Lang::txt('COM_COLLECTIONS_CHANGE_ACCESS'); ?>">
						<span><?php echo $row->get('groupname'); ?></span>
					</a>
				<?php } else { ?>
					<span class="access <?php echo $color_access; ?>">
						<span><?php echo $row->get('groupname'); ?></span>
					</span>
				<?php } ?>
				</td>
				<td class="priority-3">
					<span class="scope">
						<span><?php echo $this->escape($row->get('object_type')) . ' (' . $this->escape($row->get('object_id')) . ')'; ?></span>
					</span>
				</td>
				<td>
				<?php if ($row->get('posts', 0) > 0) { ?>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=posts&collection_id=' . $row->get('id')); ?>">
						<span><?php echo Lang::txt('COM_COLLECTIONS_NUM_POSTS', $row->get('posts', 0)); ?></span>
					</a>
				<?php } else { ?>
					<span>
						<span><?php echo $row->get('posts', 0); ?></span>
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
	<input type="hidden" name="task" value="<?php echo $this->task; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo JHTML::_('form.token'); ?>
</form>