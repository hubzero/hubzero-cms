<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_STATUSES'), 'support.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::deleteList();
?>
<script type="text/javascript">
/*function submitbutton(pressbutton)
{
	if (pressbutton == 'cancel') {
		submitform(pressbutton);
		return;
	}
	// do field validation
	submitform(pressbutton);
}*/
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-open"><?php echo Lang::txt('COM_SUPPORT_FILTER_FOR'); ?>:</label> 
		<select name="open" id="filter-open" onchange="document.adminForm.submit( );">
			<option value="-1"<?php if ($this->filters['open'] < 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_FOR_ALL'); ?></option>
			<option value="0"<?php if ($this->filters['open'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_FOR_CLOSED'); ?></option>
			<option value="1"<?php if ($this->filters['open'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_FOR_OPEN'); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count($this->rows);?>);" /></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_SUPPORT_COL_ID', 'id', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_SUPPORT_COL_FOR', 'open', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_SUPPORT_COL_TITLE', 'title', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_SUPPORT_COL_ALIAS', 'alias', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_SUPPORT_COL_COLOR', 'color', @$this->filters['sort_Dir'], @$this->filters['sort']); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php
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
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];
?>
			<tr>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i;?>" value="<?php echo $row->id ?>" onclick="isChecked(this.checked, this);" /></td>
				<td><?php echo $row->id; ?></td>
				<td><?php echo ($row->open ? Lang::txt('COM_SUPPORT_FOR_OPEN') : Lang::txt('COM_SUPPORT_FOR_CLOSED')); ?></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>"><?php echo $this->escape(stripslashes($row->title)); ?></a></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>"><?php echo $this->escape(stripslashes($row->alias)); ?></a></td>
				<td><span style="display:block;width:1em;height:1em;overflow:hidden;text-indent:5em;border:1px solid #999;background-color:<?php echo ($row->color ? '#' . $row->color : 'transparent'); ?>"><?php echo $row->color; ?></span></td>
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
