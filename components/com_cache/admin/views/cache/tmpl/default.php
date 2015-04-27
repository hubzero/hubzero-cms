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
defined('_JEXEC') or die;

Toolbar::title(Lang::txt('COM_CACHE_CLEAR_CACHE'), 'clear.png');
Toolbar::custom('delete', 'delete.png', 'delete_f2.png', 'JTOOLBAR_DELETE', true);
Toolbar::divider();
if (User::authorise('core.admin', 'com_cache'))
{
	Toolbar::preferences('com_cache');
}
Toolbar::divider();
Toolbar::help('clear');

$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
?>

<form action="<?php echo Route::url('index.php?option=com_cache'); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<div class="filter-select">
			<select name="filter_client_id" class="inputbox" onchange="this.form.submit()">
				<?php echo JHtml::_('select.options', \Components\Cache\Helpers\Cache::getClientOptions(), 'value', 'text', $this->state->get('clientId'));?>
			</select>
		</div>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col" class="priority-3">
					<?php echo Lang::txt('COM_CACHE_NUM'); ?>
				</th>
				<th scope="col">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo Lang::txt('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th scope="col" class="title nowrap">
					<?php echo $this->grid('sort', 'COM_CACHE_GROUP', 'group', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="center nowrap priority-2">
					<?php echo $this->grid('sort', 'COM_CACHE_NUMBER_OF_FILES', 'count', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="center">
					<?php echo $this->grid('sort', 'COM_CACHE_SIZE', 'size', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			$i = 0;
			foreach ($this->data as $folder => $item): ?>
				<tr class="row<?php echo $i % 2; ?>">
					<td class="priority-3">
						<?php echo $this->pagination->getRowOffset($i); ?>
					</td>
					<td>
						<input type="checkbox" id="cb<?php echo $i; ?>" name="cid[]" value="<?php echo $item->group; ?>" onclick="Joomla.isChecked(this.checked);" />
					</td>
					<td>
						<strong><?php echo $item->group; ?></strong>
					</td>
					<td class="center priority-2">
						<?php echo $item->count; ?>
					</td>
					<td class="center">
						<?php echo \Hubzero\Utility\Number::format('bytes', $item->size * 1024); ?>
					</td>
				</tr>
			<?php $i++; endforeach; ?>
		</tbody>
	</table>

	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="client" value="<?php echo $this->client->id; ?>" />
	<input type="hidden" name="filter_order" value="<?php echo $listOrder; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $listDirn; ?>" />

	<?php echo JHtml::_('form.token'); ?>
</form>
