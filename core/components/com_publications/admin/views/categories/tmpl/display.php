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

// No direct access
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_CATEGORIES'), 'addedit.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::publishList('changestatus', Lang::txt('COM_PUBLICATIONS_CHANGE_STATUS'));

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th class="priority-4"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_NAME'), 'name', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th class="priority-3"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'), 'contributable', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th class="priority-2"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_STATUS'), 'state', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="5"><?php
				$pageNav = $this->pagination(
					$this->total,
					$this->filters['start'],
					$this->filters['limit']
				);
				echo $pageNav->render();
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
			<tr class="<?php echo "row$k"; ?>">
				<td class="narrow">
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-4 narrow">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id[]=' . $row->id); ?>">
						<span><?php echo $this->escape($row->name); ?></span>
					</a>
					<span class="block faded">
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS') . ': ' .$this->escape($row->alias); ?> |
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_URL_ALIAS') . ': ' .$this->escape($row->url_alias); ?> |
						<?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DC_TYPE') . ': ' .$this->escape($row->dc_type); ?>
					</span>
				</td>
				<td class="priority-3 centeralign narrow">
					<span class="state <?php echo ($row->contributable == 1 ? 'yes' : 'no'); ?>">
						<span><?php echo ($row->contributable == 1 ? Lang::txt('JYES') : Lang::txt('JNO')); ?></span>
					</span>
				</td>
				<td class="priority-2 centeralign narrow">
					<span class="state <?php echo ($row->state == 1 ? 'on' : 'off'); ?>">
						<span><?php echo ($row->state == 1 ? Lang::txt('COM_PUBLICATIONS_ON') : Lang::txt('COM_PUBLICATIONS_OFF')); ?></span>
					</span>
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
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->filters['sort']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->filters['sort_Dir']; ?>" />
	<?php echo Html::input('token'); ?>
</form>