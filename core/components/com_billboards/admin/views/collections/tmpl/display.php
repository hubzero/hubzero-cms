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

// Add the toolbar items
Toolbar::title(Lang::txt('COM_BILLBOARDS_MANAGER') . ': ' . Lang::txt('COM_BILLBOARDS_COLLECTIONS'), 'addedit.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::spacer();
Toolbar::deleteList(Lang::txt('COM_BILLBOARDS_CONFIRM_DELETE'));
Toolbar::spacer();
Toolbar::help('collections');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo $this->rows->copy()->total();?>);" /></th>
				<th scope="col" class="priority-3"><?php echo Lang::txt('COM_BILLBOARDS_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_BILLBOARDS_COL_COLLECTION'); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="3"><?php echo $this->rows->pagination; ?></td>
			</tr>
		</tfoot>
		<tbody>

		<?php $i = 0; ?>
		<?php foreach ($this->rows as $row) : ?>
			<tr>
				<td><input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked, this);" /></td>
				<td class="priority-3"><?php echo $row->id; ?></td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<?php echo $this->escape(stripslashes($row->name)); ?>
					</a>
				</td>
			</tr>
			<?php $i++; ?>
		<?php endforeach; ?>

		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>