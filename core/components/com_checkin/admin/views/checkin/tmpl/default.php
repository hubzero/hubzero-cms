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

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CHECKIN_GLOBAL_CHECK_IN'), 'checkin.png');
if (User::authorise('core.admin', 'com_checkin'))
{
	Toolbar::custom('checkin', 'checkin.png', 'checkin_f2.png', 'JTOOLBAR_CHECKIN', true);
	Toolbar::divider();
	Toolbar::preferences('com_checkin');
	Toolbar::divider();
}
Toolbar::help('checkin');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>
<form action="<?php echo Route::url('index.php?option=com_checkin'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-search fltlft">
			<label class="filter-search-lbl" for="filter_search"><?php echo Lang::txt('JSEARCH_FILTER_LABEL'); ?></label>
			<input type="text" name="filter_search" id="filter_search" value="<?php echo $this->escape($this->state->get('filter.search')); ?>" placeholder="<?php echo Lang::txt('COM_CHECKIN_FILTER_SEARCH_DESC'); ?>" />

			<button type="submit" class="btn"><?php echo Lang::txt('JSEARCH_FILTER_SUBMIT'); ?></button>
			<button type="button" onclick="$('#filter_search').val('');this.form.submit();"><?php echo Lang::txt('JSEARCH_FILTER_CLEAR'); ?></button>
		</div>
	</fieldset>
	<div class="clr"> </div>

	<table id="global-checkin" class="adminlist">
		<thead>
			<tr>
				<th scope="col"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" /></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_CHECKIN_DATABASE_TABLE', 'table', $listDirn, $listOrder); ?></th>
				<th scope="col"><?php echo $this->grid('sort', 'COM_CHECKIN_ITEMS_TO_CHECK_IN', 'count', $listDirn, $listOrder); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php foreach ($this->items as $table => $count): $i=0; ?>
				<tr class="row<?php echo $i%2; ?>">
					<td><?php echo $this->grid('id', $i, $table); ?></td>
					<td><?php echo Lang::txt('COM_CHECKIN_TABLE', $table); ?></td>
					<td><?php echo $count; ?></td>
				</tr>
			<?php endforeach; ?>
		</tbody>
		<tfoot>
			<tr>
				<td colspan="3">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo Html::input('token'); ?>
</form>
