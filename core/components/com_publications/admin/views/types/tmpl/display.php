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

Toolbar::title(Lang::txt('COM_PUBLICATIONS_PUBLICATIONS') . ': ' . Lang::txt('COM_PUBLICATIONS_MASTER_TYPES'), 'addedit.png');
Toolbar::addNew();
Toolbar::editList();
Toolbar::spacer();
Toolbar::deleteList();

?>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<table class="adminlist">
		<thead>
			<tr>
				<th><input type="checkbox" name="toggle" value="" onclick="checkAll(<?php echo count( $this->rows );?>);" /></th>
				<th class="priority-3"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ID'), 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_NAME'), 'type', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th class="priority-4"><?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ALIAS'); ?></th>
				<th class="priority-3"><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_CONTRIBUTABLE'), 'contributable', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th><?php echo Html::grid('sort', Lang::txt('COM_PUBLICATIONS_FIELD_ORDER'), 'ordering', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8"><?php
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
for ($i=0, $n=count( $this->rows ); $i < $n; $i++)
{
	$row = &$this->rows[$i];
	$sClass = $row->supporting == 1 ? 'on' : 'off';
	$cClass = $row->contributable == 1 ? 'on' : 'off';

	// Determine whether master type is supported in current version of hub code
	$aClass = 'off';
	$active = 'off';

	// If we got a plugin - type is supported
	if (Plugin::isEnabled('projects', $row->alias))
	{
		$aClass = 'on';
		$active = 'on';
	}
?>
			<tr class="<?php echo "row$k"; ?>">
				<td class="narrow">
					<input type="checkbox" name="id[]" id="cb<?php echo $i; ?>" value="<?php echo $row->id; ?>" onclick="isChecked(this.checked);" />
				</td>
				<td class="priority-3 narrow">
					<?php echo $row->id; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>">
						<span><?php echo $this->escape($row->type); ?></span>
					</a>
				</td>
				<td class="priority-4">
					<span class="block faded"><?php echo $this->escape($row->alias); ?></span>
				</td>
				<td class="priority-3 centeralign narrow">
					<span class="state <?php echo $cClass; ?>">
						<span><?php echo Lang::txt($cClass); ?></span>
					</span>
				</td>
				<td class="order">
					<span>
						<?php echo $pageNav->orderUpIcon($i, (isset($this->rows[$i-1]->ordering))); ?>
					</span>
					<span>
						<?php echo $pageNav->orderDownIcon($i, $n, (isset($this->rows[$i+1]->ordering))); ?>
					</span>
					<input type="hidden" name="order[]" value="<?php echo $row->ordering; ?>" />
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