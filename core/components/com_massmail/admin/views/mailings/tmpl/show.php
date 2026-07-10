<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo  = \Components\Massmail\Admin\Helpers\Permissions::getActions();
$row    = $this->row;
$status = $row->get('status');
$Mailing = '\Components\Massmail\Models\Mailing';

$total  = (int) $row->get('total_recipients');
$sent   = (int) $row->get('sent_count');
$failed = (int) $row->get('failed_count');
$pending = max(0, $total - $sent - $failed);
$pct    = $total > 0 ? round(($sent + $failed) / $total * 100) : 0;

$token  = Session::getFormToken();
$base   = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

Toolbar::title(Lang::txt('COM_MASSMAIL_MAILING_STATUS'), 'massmail');
Toolbar::cancel('cancel', 'JTOOLBAR_BACK');
?>
<div class="mm-show">
	<h3><?php echo $this->escape(stripslashes($row->get('subject'))); ?></h3>
	<p>
		<span class="mm-status mm-status-<?php echo $status; ?>"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($status)); ?></span>
		<?php if ((int) $row->get('ignore_optout')) { ?>
			<span class="badge badge-warning"><?php echo Lang::txt('COM_MASSMAIL_IGNORES_OPTOUT'); ?></span>
		<?php } ?>
	</p>

	<div class="mm-actions">
		<?php if ($status == constant($Mailing . '::STATUS_PENDING_APPROVAL') && $canDo->get('core.approve')) { ?>
			<a class="btn btn-success" href="<?php echo Route::url($base . '&task=approve&id=' . $row->get('id') . '&' . $token . '=1'); ?>"><?php echo Lang::txt('COM_MASSMAIL_APPROVE'); ?></a>
		<?php } ?>
		<?php if ($status == constant($Mailing . '::STATUS_HELD') && $canDo->get('core.release')) { ?>
			<a class="btn btn-success" href="<?php echo Route::url($base . '&task=release&id=' . $row->get('id') . '&' . $token . '=1'); ?>"><?php echo Lang::txt('COM_MASSMAIL_RELEASE'); ?></a>
		<?php } ?>
		<?php if (in_array($status, array(constant($Mailing . '::STATUS_SENDING'), constant($Mailing . '::STATUS_PREPARING'))) && $canDo->get('core.edit.state')) { ?>
			<a class="btn" href="<?php echo Route::url($base . '&task=pause&id=' . $row->get('id') . '&' . $token . '=1'); ?>"><?php echo Lang::txt('COM_MASSMAIL_PAUSE'); ?></a>
		<?php } ?>
		<?php if ($status == constant($Mailing . '::STATUS_PAUSED') && $canDo->get('core.edit.state')) { ?>
			<a class="btn" href="<?php echo Route::url($base . '&task=resume&id=' . $row->get('id') . '&' . $token . '=1'); ?>"><?php echo Lang::txt('COM_MASSMAIL_RESUME'); ?></a>
		<?php } ?>
		<?php if (in_array($status, array(constant($Mailing . '::STATUS_SENDING'), constant($Mailing . '::STATUS_PREPARING'), constant($Mailing . '::STATUS_PAUSED'), constant($Mailing . '::STATUS_PENDING_APPROVAL'), constant($Mailing . '::STATUS_HELD'), constant($Mailing . '::STATUS_SCHEDULED'))) && $canDo->get('core.edit.state')) { ?>
			<a class="btn btn-danger" href="<?php echo Route::url($base . '&task=stop&id=' . $row->get('id') . '&' . $token . '=1'); ?>" onclick="return confirm('<?php echo Lang::txt('COM_MASSMAIL_CONFIRM_CANCEL'); ?>');"><?php echo Lang::txt('COM_MASSMAIL_CANCEL_MAILING'); ?></a>
		<?php } ?>
	</div>

	<div class="mm-progress-bar" style="background:#eee;border-radius:4px;overflow:hidden;height:22px;margin:12px 0;">
		<div style="width:<?php echo $pct; ?>%;background:#3a7;height:22px;"></div>
	</div>

	<table class="adminlist">
		<tbody>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_TOTAL_RECIPIENTS'); ?></th><td><?php echo number_format($total); ?></td></tr>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_SENT'); ?></th><td><?php echo number_format($sent); ?></td></tr>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_PENDING'); ?></th><td><?php echo number_format($pending); ?></td></tr>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_FAILED'); ?></th><td><?php echo number_format($failed); ?></td></tr>
			<?php if ($status == constant($Mailing . '::STATUS_SCHEDULED') && $row->get('scheduled_at')) { ?>
				<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_SCHEDULED_AT'); ?></th><td><?php echo Date::of($row->get('scheduled_at'))->toLocal('D, M j, Y g:i a T'); ?></td></tr>
			<?php } ?>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_STARTED'); ?></th><td><?php echo $row->get('started_on') ? Date::of($row->get('started_on'))->toLocal() : '&mdash;'; ?></td></tr>
			<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_COMPLETED'); ?></th><td><?php echo $row->get('completed_on') ? Date::of($row->get('completed_on'))->toLocal() : '&mdash;'; ?></td></tr>
			<?php if ((int) $row->get('ignore_optout') && $row->get('approved_by')) { ?>
				<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_APPROVED_BY'); ?></th><td><?php echo $this->escape(User::getInstance($row->get('approved_by'))->get('name')) . ' &middot; ' . Date::of($row->get('approved_on'))->toLocal(); ?></td></tr>
			<?php } ?>
		</tbody>
	</table>

	<?php if (count($this->failures)) { ?>
		<h4><?php echo Lang::txt('COM_MASSMAIL_FAILURES'); ?></h4>
		<table class="adminlist">
			<thead><tr><th><?php echo Lang::txt('COM_MASSMAIL_EMAIL'); ?></th><th><?php echo Lang::txt('COM_MASSMAIL_ATTEMPTS'); ?></th><th><?php echo Lang::txt('COM_MASSMAIL_ERROR'); ?></th></tr></thead>
			<tbody>
			<?php foreach ($this->failures as $f) { ?>
				<tr><td><?php echo $this->escape($f->get('email')); ?></td><td><?php echo (int) $f->get('attempts'); ?></td><td><?php echo $this->escape($f->get('error')); ?></td></tr>
			<?php } ?>
			</tbody>
		</table>
	<?php } ?>
</div>
