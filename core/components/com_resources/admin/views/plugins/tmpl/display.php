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

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Resources\Helpers\Permissions::getActions('plugin');

Toolbar::title(Lang::txt('COM_RESOURCES') . ': ' . Lang::txt('COM_RESOURCES_PLUGINS'), 'plugin.png');
if ($canDo->get('core.edit.state'))
{
	Toolbar::publishList();
	Toolbar::unpublishList();
}
?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<?php echo $this->states; ?>

		<input type="submit" name="filter_submit" id="filter_submit" value="<?php echo Lang::txt('COM_RESOURCES_GO'); ?>" />
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows); ?>);" />
				</th>
				<th scope="col" class="priority-5">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_ID', 'p.extension_id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_PLUGIN_NAME', 'p.name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_PUBLISHED', 'p.published', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col" class="priority-2">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_ORDER', 'p.folder', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
					<?php echo $this->grid('order', $this->rows); ?>
				</th>
				<th scope="col" class="priority-3">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_ACCESS', 'groupname', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_RESOURCES_COL_MANAGE'); ?>
				</th>
				<th scope="col" class="priority-4">
					<?php echo $this->grid('sort', 'COM_RESOURCES_COL_FILE', 'p.element', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
				$pagination = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				echo $pagination->render();
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
$db = JFactory::getDBO();
$tbl = new JTableExtension($db);

$k = 0;
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$link = Route::url('index.php?option=com_plugins&task=plugin.edit&extension_id='.$row->id.'&component=resources');

	//$access    = $this->grid('access', $row, $i);
	//$checked = $this->grid('checkedout', $row, $i);
	$published = $this->grid('published', $row, $i);

	$ordering = ($this->filters['sort'] == 'p.folder');

	switch ($row->published)
	{
		case '2':
			$task = 'publish';
			$img  = 'disabled.png';
			$alt  = Lang::txt('JTRASHED');
			$cls  = 'trashed';
		break;
		case '1':
			$task = 'unpublish';
			$img  = 'publish_g.png';
			$alt  = Lang::txt('JPUBLISHED');
			$cls  = 'publish';
		break;
		case '0':
		default:
			$task = 'publish';
			$img  = 'publish_x.png';
			$alt  = Lang::txt('JUNPUBLISHED');
			$cls  = 'unpublish';
		break;
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" />
				</td>
				<td class="priority-5">
					<?php echo $row->id; ?>
				</td>
				<td>
					<?php
						if ($tbl->isCheckedOut(User::get('id'), $row->checked_out)) {
							echo $this->escape($row->name);
						} else {
					?>
						<a class="editlinktip hasTip" href="<?php echo $link; ?>">
							<span><?php echo $this->escape($row->name); ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php if ($tbl->isCheckedOut(User::get('id'), $row->checked_out)) { ?>
						<span class="state <?php echo $cls; ?>">
							<span class="text"><?php echo $alt; ?></span>
						</span>
					<?php } else { ?>
						<a class="state <?php echo $cls; ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=' . $task . '&id=' . $row->id . '&' . Session::getFormToken() . '=1'); ?>" title="<?php echo Lang::txt('COM_RESOURCES_SET_TASK_TO', $task); ?>">
							<span class="text"><?php echo $alt; ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="priority-2 order">
					<span><?php echo $pagination->orderUpIcon($i, ($row->folder == @$this->rows[$i-1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderup', 'COM_RESOURCES_MOVE_UP', $row->ordering); ?></span>
					<span><?php echo $pagination->orderDownIcon($i, $n, ($row->folder == @$this->rows[$i+1]->folder && $row->ordering > -10000 && $row->ordering < 10000), 'orderdown', 'COM_RESOURCES_MOVE_DOWN', $row->ordering); ?></span>
					<input type="text" name="order[]" size="5" value="<?php echo $row->ordering; ?>" <?php echo ($row->ordering ? '' : 'disabled="disabled"'); ?> class="text_area" style="text-align: center" />
				</td>
				<td class="priority-3">
					<?php echo $this->escape($row->access_level); ?>
				</td>
				<td>
					<?php if (in_array($row->element, $this->manage)) { ?>
						<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=manage&plugin=' . $row->element); ?>">
							<span><?php echo Lang::txt('COM_RESOURCES_COL_MANAGE'); ?></span>
						</a>
					<?php } ?>
				</td>
				<td class="priority-4">
					<?php echo $this->escape($row->element); ?>
				</td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />

	<?php echo Html::input('token'); ?>
</form>