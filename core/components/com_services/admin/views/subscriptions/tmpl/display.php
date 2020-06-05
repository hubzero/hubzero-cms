<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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
$this->css();
$this->css('admin.subscriptions.css');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option  . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<fieldset id="filter-bar">
		<label for="filter-status"><?php echo Lang::txt('COM_SERVICES_FILTER_BY'); ?>:</label>
		<select name="filter_status" id="filter-status" class="filter filter-submit">
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
					$status = ($row->expires > $now) ? '<span class="service-active">' . strtolower(Lang::txt('COM_SERVICES_STATE_ACTIVE')) . '</span>' : '<span  class="service-expired">' . strtolower(Lang::txt('COM_SERVICES_EXPIRED')) . '</span>';
					break;
				case '0':
					$status = '<span class="service-pending">' . strtolower(Lang::txt('COM_SERVICES_STATE_PENDING')) . '</span>';
					break;
				case '2':
					$status = '<span class="service-cancelled">' . strtolower(Lang::txt('COM_SERVICES_STATE_CANCELED')) . '</span>';
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
					<?php echo $row->pendingpayment && ($row->pendingpayment > 0 or $row->pendingunits > 0)  ? '<span class="service-pending">' . $pending . '</span>' : $pending; ?>
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
