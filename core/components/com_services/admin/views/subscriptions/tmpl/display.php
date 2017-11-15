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

$canDo = \Components\Services\Helpers\Permissions::getActions('service');

Toolbar::title(Lang::txt('COM_SERVICES') . ': ' . Lang::txt('COM_SERVICES_SUBSCRIPTIONS'), 'services');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences('com_services', '550');
}

$now = Date::toSql();

// Push some styles to the template
$this->css('admin.subscriptions.css');
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

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-status"><?php echo Lang::txt('COM_SERVICES_FILTER_BY'); ?>:</label>
		<select name="filter_status" id="filter-status" onchange="document.adminForm.submit( );">
			<option value="pending"<?php if ($this->filters['status'] == 'pending') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_PENDING'); ?></option>
			<option value="active"<?php if ($this->filters['status'] == 'processed') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_ACTIVE'); ?></option>
			<option value="cancelled"<?php if ($this->filters['status'] == 'cancelled') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_CANCELLED'); ?></option>
			<option value="all"<?php if ($this->filters['status'] == 'all') { echo ' selected="selected"'; } ?>><?php echo Lang::txt('COM_SERVICES_FILTER_BY_ALL'); ?></option>
		</select>
	</fieldset>

	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_ID_CODE', 'id', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_STATUS', 'status', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_SERVICE'); ?></th>
				<th scope="col"><?php echo Lang::txt('COM_SERVICES_COL_PENDING'); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_USER', 'uid', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_ADDED', 'added', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_LAST_UPDATED', 'updated', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
				<th scope="col"><?php echo Html::grid('sort', 'COM_SERVICES_COL_EXPIRES', 'expires', @$this->filters['sort_Dir'], @$this->filters['sort'] ); ?></th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php
					// Initiate paging
					echo $this->rows->pagination;
					?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$i = 0;
		$k = 0;
		foreach ($this->rows as $row)
		{
			$name  = Lang::txt('COM_SERVICES_UNKNOWN');
			$login = Lang::txt('COM_SERVICES_UNKNOWN');
			$ruser = User::getInstance($row->uid);
			if ($ruser->get('id'))
			{
				$name  = $ruser->get('name');
				$login = $ruser->get('username');
			}

			$status = '';
			$pending = Lang::txt('COM_SERVICES_FOR_UNITS', $row->currency . ' ' . $row->pendingpayment, $row->pendingunits);

			$expires = (intval($row->expires) <> 0) ? Date::of($row->expires)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_SERVICES_NOT_APPLICABLE');

			switch ($row->status)
			{
				case '1':
					$status = ($row->expires > $now) ? '<span style="color:#197f11;">' . strtolower(Lang::txt('COM_SERVICES_STATE_ACTIVE')) . '</span>' : '<span style="color:#ef721e;">' . strtolower(Lang::txt('COM_SERVICES_EXPIRED')) . '</span>';
					break;
				case '0':
					$status = '<span style="color:#ff0000;">' . strtolower(Lang::txt('COM_SERVICES_STATE_PENDING')) . '</span>';
					break;
				case '2':
					$status = '<span style="color:#999;">' . strtolower(Lang::txt('COM_SERVICES_STATE_CANCELED')) . '</span>';
					$pending .= $row->pendingpayment ? ' (' . Lang::txt('COM_SERVICES_REFUND') . ')' : '';
					break;
			}
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_SERVICES_VIEW_SUBSCRIPTION_DETAILS'); ?>">
						<?php echo $row->id . ' -- ' . $row->code; ?>
					</a>
				</td>
				<td>
					<?php echo $status; ?>
				</td>
				<td>
					<a href="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller . '&task=edit&id=' . $row->id); ?>" title="<?php echo Lang::txt('COM_SERVICES_VIEW_SUBSCRIPTION_DETAILS'); ?>">
						<span><?php echo $this->escape($row->category) . ' -- ' . $this->escape($row->title); ?></span>
					</a>
				</td>
				<td>
					<?php echo $row->pendingpayment && ($row->pendingpayment > 0 or $row->pendingunits > 0)  ? '<span style="color:#ff0000;">' . $pending . '</span>' : $pending; ?>
				</td>
				<td>
					<?php echo $name . ' (' . $login . ')'; ?>
				</td>
				<td>
					<?php echo Date::of($row->added)->toLocal(Lang::txt('DATE_FORMAT_HZ1')); ?>
				</td>
				<td>
					<?php echo $row->updated ? Date::of($row->updated)->toLocal(Lang::txt('DATE_FORMAT_HZ1')) : Lang::txt('COM_SERVICES_NEVER'); ?>
				</td>
				<td>
					<?php echo $expires; ?>
				</td>
			</tr>
			<?php
			$i++;
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" autocomplete="off" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>
