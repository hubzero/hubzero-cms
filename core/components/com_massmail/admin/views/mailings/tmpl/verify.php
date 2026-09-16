<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$row   = $this->row;
$disp  = $this->disposition;
$token = Session::getFormToken();
$base  = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

// Meta block (mirrors the edit/view page).
$status      = $row->get('status');
$creatorName = $row->creator ? $row->creator->get('name') : '';
$fmt         = 'M j, Y g:i a';

$fromName  = trim((string) $row->get('from_name'))  ?: Config::get('fromname');
$fromEmail = trim((string) $row->get('from_email')) ?: Config::get('mailfrom');
$replyTo   = trim((string) $row->get('reply_to'));
$fromShown = $fromEmail ? ($fromName ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail) : $fromName;

// Delivery line.
if ($disp['scheduled'])
{
	$deliveryLine = Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY_SCHEDULED', $disp['scheduled_local']);
}
else
{
	$deliveryLine = Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY_NOW');
}

// What accepting will do -- approval takes precedence over hold/schedule/send.
$needsApproval = $disp['needs_general_approval'] || $disp['needs_optout_approval'];

// Small inline icons for the action buttons (decorative; the text carries the meaning).
$icSend  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>';
$icEdit  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10ZM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5Zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5Zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325Z"/></svg>';
$icSave  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M11 2H9v3h2V2Z"/><path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0ZM1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5Zm3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4v4.5ZM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5V15Z"/></svg>';
$icTrash = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/></svg>';

Toolbar::title(Lang::txt('COM_MASSMAIL_VERIFY_TITLE'), 'massmail');
Toolbar::cancel('cancel', 'JTOOLBAR_BACK');

$this->css('verify.css', 'com_massmail');
$this->js('verify.js', 'com_massmail');
?>
<form action="<?php echo Route::url($base); ?>" method="post" name="adminForm" id="adminForm" class="mm-verify">
	<p class="mm-intro"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_INTRO'); ?></p>

	<div class="mm-grid">
		<div class="mm-main">
			<div class="mm-card">
				<div class="mm-pv-head">
					<h4><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_HEADING'); ?></h4>
					<span class="mm-pv-toggle">
						<button type="button" id="mm-pv-btn-html" class="is-active"><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_HTML'); ?></button>
						<button type="button" id="mm-pv-btn-text"><?php echo Lang::txt('COM_MASSMAIL_PREVIEW_TEXT'); ?></button>
					</span>
				</div>
				<div class="mm-env">
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_FROM'); ?></span><span class="mm-env-val"><?php echo $this->escape($fromShown); ?></span></div>
					<?php if ($replyTo) { ?>
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></span><span class="mm-env-val"><?php echo $this->escape($replyTo); ?></span></div>
					<?php } ?>
					<div class="mm-env-row"><span class="mm-env-label"><?php echo Lang::txt('COM_MASSMAIL_SUBJECT'); ?></span><span class="mm-env-val"><?php echo $this->escape(stripslashes((string) $row->get('subject'))); ?></span></div>
				</div>
				<div class="mm-pv-body">
					<iframe id="mm-pv-html" sandbox="" title="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_PREVIEW_HTML')); ?>" srcdoc="<?php echo $this->escape($this->previewHtml); ?>"></iframe>
					<pre id="mm-pv-text" hidden><?php echo $this->escape($this->previewText); ?></pre>
				</div>
			</div>

			<div class="mm-verify-actions">
				<button type="button" class="btn mm-btn-accept mm-submit" data-task="launch"><?php echo $icSend . Lang::txt('COM_MASSMAIL_ACCEPT_SEND'); ?></button>
				<a class="btn mm-btn-neutral" href="<?php echo Route::url($base . '&task=edit&id=' . (int) $row->get('id')); ?>"><?php echo $icEdit . Lang::txt('COM_MASSMAIL_EDIT_MESSAGE'); ?></a>
				<span class="spacer"></span>
				<a class="btn mm-btn-neutral" href="<?php echo Route::url($base); ?>"><?php echo $icSave . Lang::txt('COM_MASSMAIL_SAVE_DRAFT'); ?></a>
				<a class="btn mm-btn-danger mm-confirm" data-confirm="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_CONFIRM_DISCARD')); ?>" href="<?php echo Route::url($base . '&task=discard&id=' . (int) $row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icTrash . Lang::txt('COM_MASSMAIL_DISCARD'); ?></a>
			</div>
		</div>

		<div class="mm-side">
			<div class="mm-card mm-status-card">
				<h4><?php echo Lang::txt('COM_MASSMAIL_DETAILS'); ?></h4>
				<table class="meta mm-meta">
					<tbody>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_FIELD_ID'); ?></th>
							<td><?php echo (int) $row->get('id'); ?></td>
						</tr>
						<tr>
							<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_STATUS'); ?></th>
							<td><span class="mm-status mm-status-<?php echo $status; ?>"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($status)); ?></span></td>
						</tr>
						<?php if ($row->get('created') && $row->get('created') != '0000-00-00 00:00:00') { ?>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_CREATED'); ?></th>
								<td><?php echo Date::of($row->get('created'))->toLocal($fmt); ?></td>
							</tr>
						<?php } ?>
						<?php if ($creatorName) { ?>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_CREATED_BY'); ?></th>
								<td><?php echo $this->escape($creatorName); ?></td>
							</tr>
						<?php } ?>
						<?php if ($row->get('modified') && $row->get('modified') != '0000-00-00 00:00:00') { ?>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_MODIFIED'); ?></th>
								<td><?php echo Date::of($row->get('modified'))->toLocal($fmt); if ($row->get('modified_by')) { echo '<br /><span class="mm-meta-sub">' . Lang::txt('COM_MASSMAIL_META_BY', $this->escape(User::getInstance($row->get('modified_by'))->get('name'))) . '</span>'; } ?></td>
							</tr>
						<?php } ?>
					</tbody>
				</table>
			</div>

			<div class="mm-card mm-sum">
				<h4><?php echo Lang::txt('COM_MASSMAIL_VERIFY_SUMMARY'); ?></h4>
				<div class="mm-sum-row">
					<span class="k"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_RECIPIENTS'); ?></span>
					<span class="v mm-sum-count"><?php echo number_format($this->preview); ?></span>
				</div>
				<div class="mm-sum-row">
					<span class="k"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_DELIVERY'); ?></span>
					<span class="v"><?php echo $this->escape($deliveryLine); ?></span>
				</div>

				<?php if ($needsApproval) { ?>
					<div class="mm-disp mm-disp-approve">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE'); ?></span>
						<?php if ($disp['needs_general_approval']) { ?>
							<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE_GENERAL'); ?></span>
						<?php } ?>
						<?php if ($disp['needs_optout_approval']) { ?>
							<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_APPROVE_OPTOUT'); ?></span>
						<?php } ?>
					</div>
				<?php } elseif ($disp['scheduled'] && !$disp['held']) { ?>
					<div class="mm-disp mm-disp-send">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_STATUS_SCHEDULED'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_SCHEDULE'); ?></span>
					</div>
				<?php } elseif (!$disp['held']) { ?>
					<div class="mm-disp mm-disp-send">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_STATUS_SENDING'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_SEND'); ?></span>
					</div>
				<?php } ?>

				<?php // Whenever the system is off, warn that it will be held and needs release -- shown alongside the approval banner too. ?>
				<?php if ($disp['held']) { ?>
					<div class="mm-disp mm-disp-hold">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_HELD_TITLE'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_HOLD'); ?></span>
					</div>
				<?php } ?>

				<?php if ($disp['needs_optout_approval'] && !$needsApproval) { ?>
					<?php /* Only when the approval banner isn't already explaining the override. */ ?>
					<div class="mm-optout-flag">&#9888; <?php echo Lang::txt('COM_MASSMAIL_VERIFY_OPTOUT_FLAG'); ?></div>
				<?php } ?>
			</div>
		</div>
	</div>


	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo (int) $row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<?php echo Html::input('token'); ?>
</form>
