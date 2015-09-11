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

// No direct access.
defined('_HZEXEC_') or die();

Toolbar::title(Lang::txt('COM_SUPPORT_TICKETS') . ': ' . Lang::txt('COM_SUPPORT_ABUSE_REPORTS'), 'support.png');

Html::behavior('framework');

$this->view('_submenu')->display();
?>
<script type="text/javascript">
function submitbutton(pressbutton)
{
	var form = document.adminForm;
	if (pressbutton == 'cancel') {
		submitform( pressbutton );
		return;
	}
	// do field validation
	submitform( pressbutton );
}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-state"><?php echo Lang::txt('COM_SUPPORT_SHOW'); ?>:</label>
		<select name="state" id="filter-state" onchange="document.adminForm.submit( );">
			<option value="0"<?php if ($this->filters['state'] == 0) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_OUTSTANDING'); ?></option>
			<option value="1"<?php if ($this->filters['state'] == 1) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_RELEASED'); ?></option>
			<option value="2"<?php if ($this->filters['state'] == 2) { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_DELETED'); ?></option>
		</select>

		<label for="filter-sortby"><?php echo Lang::txt('COM_SUPPORT_SORT_BY'); ?>:</label>
		<select name="sortby" id="filter-sortby" onchange="document.adminForm.submit( );">
			<option value="a.category"<?php if ($this->filters['sortby'] == 'a.category') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_SORT_CATEGORY'); ?></option>
			<option value="a.created DESC"<?php if ($this->filters['sortby'] == 'a.created DESC') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SUPPORT_SORT_MOST_RECENT'); ?></option>
		</select>
	</fieldset>
	<div class="clr"></div>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_ID'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_STATUS'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_REPORTED_ITEM'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_REASON'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_BY'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SUPPORT_COL_DATE'); ?></th>
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
for ($i=0, $n=count($this->rows); $i < $n; $i++)
{
	$row = &$this->rows[$i];

	$status = '';
	switch ($row->state)
	{
		case '1':
			$status = Lang::txt('COM_SUPPORT_REPORT_RELEASED');
			break;
		case '0':
			$status = Lang::txt('COM_SUPPORT_REPORT_NEW');
			break;
	}

	$user = User::getInstance($row->created_by);
?>
			<tr class="<?php echo "row$k"; ?>">
				<td><?php echo $row->id;  ?></td>
				<td><?php echo $status;  ?></td>
				<td><a href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=view&id=' . $row->id . '&cat=' . $row->category); ?>"><?php echo ($row->category . ' #' . $row->referenceid); ?></a></td>
				<td><?php echo $this->escape($row->subject); ?></td>
				<td><?php echo $this->escape($user->get('username')); ?></td>
				<td><?php echo Date::of($row->created)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?></td>
			</tr>
<?php
	$k = 1 - $k;
}
?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
	<input type="hidden" name="task" value="display" />

	<?php echo Html::input('token'); ?>
</form>
