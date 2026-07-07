<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Massmail\Admin\Helpers\Permissions::getActions();

Toolbar::title(Lang::txt('COM_MASSMAIL') . ': ' . Lang::txt('COM_MASSMAIL_DASHBOARD'), 'massmail');
if ($canDo->get('core.admin'))
{
	Toolbar::preferences($this->option, '550');
}

$sendingOn = (int) $this->params->get('sending_enabled', 0);
$cronOn    = ($this->cron && (int) $this->cron->state == 1);
$queue     = $this->liveQueue;
?>
<div class="mm-dashboard">

	<?php if (!$sendingOn) { ?>
		<p class="warning"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_DISABLED'); ?></p>
	<?php } ?>
	<?php if (!$cronOn) { ?>
		<p class="warning"><?php echo Lang::txt('COM_MASSMAIL_DASH_CRON_WARNING'); ?></p>
	<?php } ?>

	<table class="adminlist">
		<tbody>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENDING_ENABLED'); ?></th>
				<td><?php echo $sendingOn ? Lang::txt('JYES') : Lang::txt('JNO'); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_HOURLY_LIMIT'); ?></th>
				<td><?php echo (int) $this->params->get('hourly_limit', 300); ?> / hour</td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_SENT_LAST_HOUR'); ?></th>
				<td><?php echo number_format($this->sentLastHour); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_QUEUE_SIZE'); ?></th>
				<td>
					<?php echo $queue['available'] ? number_format($queue['size']) : Lang::txt('COM_MASSMAIL_DASH_QUEUE_UNREADABLE'); ?>
					<?php echo ' (' . Lang::txt('COM_MASSMAIL_DASH_WATERMARK') . ': ' . (int) $this->params->get('queue_watermark', 150) . ')'; ?>
				</td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_LAST_CHECKED'); ?></th>
				<td><?php echo $this->snapshot->get('last_checked') ? Date::of($this->snapshot->get('last_checked'))->toLocal() : '&mdash;'; ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_CRON_LAST_RUN'); ?></th>
				<td><?php echo ($this->cron && $this->cron->last_run) ? Date::of($this->cron->last_run)->toLocal() : '&mdash;'; ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_TOTAL_MAILINGS'); ?></th>
				<td><?php echo number_format($this->totalMailings); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_ACTIVE_MAILINGS'); ?></th>
				<td><?php echo number_format($this->activeMailings); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_SENT'); ?></th>
				<td><?php echo number_format($this->messagesSent); ?></td>
			</tr>
			<tr>
				<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_DASH_MESSAGES_PENDING'); ?></th>
				<td><?php echo number_format($this->messagesPending); ?></td>
			</tr>
		</tbody>
	</table>
</div>
