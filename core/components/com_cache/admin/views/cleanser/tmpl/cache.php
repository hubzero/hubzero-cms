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

// no direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_CACHE_CLEAR_CACHE'), 'clear');
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
				<?php foreach (\Components\Cache\Helpers\Helper::getClientOptions() as $option) : ?>
					<option value="<?php echo $option->value; ?>"<?php if ($option->value == $this->state->get('clientId')) { echo ' selected="selected"'; } ?>><?php echo $this->escape($option->text); ?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</fieldset>

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
					<?php echo Html::grid('sort', 'COM_CACHE_GROUP', 'group', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="center nowrap priority-2">
					<?php echo Html::grid('sort', 'COM_CACHE_NUMBER_OF_FILES', 'count', $listDirn, $listOrder); ?>
				</th>
				<th scope="col" class="center">
					<?php echo Html::grid('sort', 'COM_CACHE_SIZE', 'size', $listDirn, $listOrder); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5">
					<?php echo $this->pagination->render(); ?>
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
						<?php echo \Hubzero\Utility\Number::formatBytes($item->size * 1024); ?>
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

	<?php echo Html::input('token'); ?>
</form>
