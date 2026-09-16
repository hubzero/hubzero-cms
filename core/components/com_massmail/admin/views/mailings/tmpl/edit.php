<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

use Components\Massmail\Helpers\Recipients;

$canDo = \Components\Massmail\Admin\Helpers\Permissions::getActions();

$isNew = $this->row->isNew();
$spec  = Recipients::normalizeSpec(json_decode((string) $this->row->get('recipient_spec'), true));
$ignore = (int) $this->row->get('ignore_optout');
$sendingOn = (int) \Component::params('com_massmail')->get('sending_enabled', 0);
$atts = $this->row->attachments()->rows();

// Editable right up until the mailing starts sending; after that this page is a
// read-only view of the frozen message. Either way the status side-panel and
// its admin controls are shown.
$editable = isset($this->editable) ? (bool) $this->editable : $this->row->isEditable();
$status   = $this->row->get('status');
$Mailing  = '\Components\Massmail\Models\Mailing';
$token    = Session::getFormToken();
$base     = 'index.php?option=' . $this->option . '&controller=' . $this->controller;

// Delivery stats (shown in the status panel once a mailing has been launched).
$total   = (int) $this->row->get('total_recipients');
$sentN   = (int) $this->row->get('sent_count');
$failedN = (int) $this->row->get('failed_count');
$pendingN = max(0, $total - $sentN - $failedN);
$pct     = $total > 0 ? round(($sentN + $failedN) / $total * 100) : 0;

// Fields flagged by a failed "Send" so they can be highlighted here.
$invalid    = isset($this->invalid) ? (array) $this->invalid : array();
$badSubject = in_array('subject', $invalid);
$badBody    = in_array('body', $invalid);
$badRcpt    = in_array('recipients', $invalid);

// The From address is configured hub-wide in the component options (the mail
// server has to be set up to send as it), so every mailing sends as that
// address. It's shown here read-only, formatted "Name <email>". Reply-to is
// still per-mailing and only a Super Administrator may set it.
$isSuper = User::authorise('core.admin');

$mmParams  = \Component::params('com_massmail');
$fromName  = trim((string) $mmParams->get('from_name'))  ?: Config::get('fromname');
$fromEmail = trim((string) $mmParams->get('from_email')) ?: Config::get('mailfrom');
$fromShown = $fromEmail ? ($fromName ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail) : $fromName;
$replyTo   = trim((string) $this->row->get('reply_to'));

// Scheduled time (stored UTC) shown back in the viewer's local zone for the picker.
$scheduledLocal = '';
$schedSaved = (string) $this->row->get('scheduled_at');
if ($schedSaved !== '' && $schedSaved !== '0000-00-00 00:00:00')
{
	$scheduledLocal = Date::of($schedSaved)->toLocal('Y-m-d H:i:s');
}
$isScheduled = ($scheduledLocal !== '');

// --- Build the To: chips + the hidden field values they serialize back to ----
$gChips = Recipients::describeGroups($spec['groups']);
$uChips = Recipients::describeUsers($spec['users']);
$emails = array_values(array_filter(array_map('trim', (array) $spec['emails']), 'strlen'));

$toChips = array();
if (!empty($spec['all']))
{
	$toChips[] = array('kind' => 'all', 'token' => '1', 'label' => Lang::txt('COM_MASSMAIL_EVERYONE'));
}
foreach ($gChips as $g) { $toChips[] = array('kind' => 'group', 'token' => $g['token'], 'label' => $g['label']); }
foreach ($uChips as $u) { $toChips[] = array('kind' => 'user',  'token' => $u['token'], 'label' => $u['label']); }
foreach ($emails as $em) { $toChips[] = array('kind' => 'email', 'token' => $em, 'label' => $em); }

$hidAll    = !empty($spec['all']) ? '1' : '0';
$hidGroups = implode(',', array_map(function ($g) { return $g['token']; }, $gChips));
$hidUsers  = implode(',', array_map(function ($u) { return $u['token']; }, $uChips));
$hidEmails = implode(',', $emails);

$chipIcon = array('all' => '★', 'group' => '👥', 'user' => '👤', 'email' => '✉');

// Live audience preview
$preview = Recipients::previewCount($spec, $ignore ? true : false);

// Recipient state + labels handed to the compose JS via data attributes.
$rcptState = array(
	'all'    => !empty($spec['all']),
	'groups' => array_map(function ($g) { return array('token' => (string) $g['token'], 'label' => $g['label']); }, $gChips),
	'users'  => array_map(function ($u) { return array('token' => (string) $u['token'], 'label' => $u['label']); }, $uChips),
	'emails' => array_values($emails),
);
$composeLang = array(
	'everyone' => Lang::txt('COM_MASSMAIL_EVERYONE'),
	'none'     => Lang::txt('COM_MASSMAIL_NO_RECIPIENTS_YET'),
	'edit'     => Lang::txt('COM_MASSMAIL_EDIT_RECIPIENTS'),
	'add'      => Lang::txt('COM_MASSMAIL_ADD_RECIPIENTS'),
	'optout'   => Lang::txt('COM_MASSMAIL_OPTOUT_PILL'),
	'attach'   => Lang::txt('COM_MASSMAIL_ADD_ATTACHMENT'),
);

// Small inline icons for the action buttons (decorative; the text carries the meaning).
$icSend  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M15.854.146a.5.5 0 0 1 .11.54l-5.819 14.547a.75.75 0 0 1-1.329.124l-3.178-4.995L.643 7.184a.75.75 0 0 1 .124-1.33L15.314.037a.5.5 0 0 1 .54.11ZM6.636 10.07l2.761 4.338L14.13 2.576 6.636 10.07Zm6.787-8.201L1.591 6.602l4.339 2.76 7.494-7.493Z"/></svg>';
$icSave  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M11 2H9v3h2V2Z"/><path d="M1.5 0h11.586a1.5 1.5 0 0 1 1.06.44l1.415 1.414A1.5 1.5 0 0 1 16 2.914V14.5a1.5 1.5 0 0 1-1.5 1.5h-13A1.5 1.5 0 0 1 0 14.5v-13A1.5 1.5 0 0 1 1.5 0ZM1 1.5v13a.5.5 0 0 0 .5.5H2v-4.5A1.5 1.5 0 0 1 3.5 9h9a1.5 1.5 0 0 1 1.5 1.5V15h.5a.5.5 0 0 0 .5-.5V2.914a.5.5 0 0 0-.146-.353l-1.415-1.415A.5.5 0 0 0 13.086 1H13v4.5A1.5 1.5 0 0 1 11.5 7h-7A1.5 1.5 0 0 1 3 5.5V1H1.5a.5.5 0 0 0-.5.5Zm3 4a.5.5 0 0 0 .5.5h7a.5.5 0 0 0 .5-.5V1H4v4.5ZM3 15h10v-4.5a.5.5 0 0 0-.5-.5h-9a.5.5 0 0 0-.5.5V15Z"/></svg>';
$icTrash = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M5.5 5.5A.5.5 0 0 1 6 6v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm2.5 0a.5.5 0 0 1 .5.5v6a.5.5 0 0 1-1 0V6a.5.5 0 0 1 .5-.5Zm3 .5a.5.5 0 0 0-1 0v6a.5.5 0 0 0 1 0V6Z"/><path d="M14.5 3a1 1 0 0 1-1 1H13v9a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V4h-.5a1 1 0 0 1-1-1V2a1 1 0 0 1 1-1H6a1 1 0 0 1 1-1h2a1 1 0 0 1 1 1h3.5a1 1 0 0 1 1 1v1ZM4.118 4 4 4.059V13a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V4.059L11.882 4H4.118ZM2.5 3h11V2h-11v1Z"/></svg>';
// Status-control icons.
$icCheck = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M12.736 3.97a.733.733 0 0 1 1.047 0 .746.746 0 0 1 0 1.052l-7.42 7.482a.733.733 0 0 1-1.047 0L2.217 9.386a.746.746 0 1 1 1.048-1.052l2.576 2.599 6.895-6.963Z"/></svg>';
$icPause = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M5.5 3.5A1.5 1.5 0 0 0 4 5v6a1.5 1.5 0 0 0 3 0V5a1.5 1.5 0 0 0-1.5-1.5Zm5 0A1.5 1.5 0 0 0 9 5v6a1.5 1.5 0 0 0 3 0V5a1.5 1.5 0 0 0-1.5-1.5Z"/></svg>';
$icPlay  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M4.5 3.5v9a.5.5 0 0 0 .765.424l7-4.5a.5.5 0 0 0 0-.848l-7-4.5A.5.5 0 0 0 4.5 3.5Z"/></svg>';
$icX     = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1Zm2.828 8.243a.75.75 0 0 1-1.06 1.06L8 8.536 6.232 10.303a.75.75 0 1 1-1.06-1.06L6.94 7.475 5.172 5.707a.75.75 0 0 1 1.06-1.06L8 6.414l1.768-1.767a.75.75 0 0 1 1.06 1.06L9.06 7.475l1.768 1.768Z"/></svg>';
$icEdit  = '<svg viewBox="0 0 16 16" width="14" height="14" aria-hidden="true" fill="currentColor"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10ZM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5Zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5Zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.468-.325Z"/></svg>';

Toolbar::title(Lang::txt($isNew ? 'COM_MASSMAIL_NEW_MAILING' : ($editable ? 'COM_MASSMAIL_EDIT_MAILING' : 'COM_MASSMAIL_MAILING_STATUS')), 'massmail');
if ($editable)
{
	Toolbar::apply();
	Toolbar::save();
	if (!$isNew && $canDo->get('core.send'))
	{
		Toolbar::custom('sendtest', 'preview', 'preview', Lang::txt('COM_MASSMAIL_SEND_TEST'), false);
		// "Send" goes through the review/verification page, not straight to dispatch.
		Toolbar::custom('review', 'upload', 'upload', Lang::txt('COM_MASSMAIL_LAUNCH'), false);
	}
}
Toolbar::cancel();

$this->css('edit.css', 'com_massmail');
$this->js('edit.js', 'com_massmail');
?>

<div class="mm-compose">

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="hz-form">
		<div class="mm-grid">
			<div class="mm-main">
				<div class="mm-card mm-mail">
					<div class="mm-mail-headers">
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_TO'); ?></span>
							<div class="mm-to-display<?php echo $badRcpt ? ' mm-invalid' : ''; ?>" id="mm-to-display">
								<?php if ($editable) { ?>
									<?php // Filled in by the compose JS from the recipient config. ?>
									<ul class="mm-to-chips" id="mm-to-summary"></ul>
									<button type="button" class="mm-to-edit" id="mm-to-edit"><?php echo Lang::txt('COM_MASSMAIL_EDIT_RECIPIENTS'); ?></button>
									<?php if ($badRcpt) { ?><span class="mm-required-note"><?php echo Lang::txt('COM_MASSMAIL_REQUIRED'); ?></span><?php } ?>
								<?php } else { ?>
									<?php // Read-only view: render the saved audience as static chips. ?>
									<ul class="mm-to-chips">
										<?php foreach ($toChips as $c) { ?>
											<li class="mm-to-chip"><span class="mm-chip-ic" aria-hidden="true"><?php echo $chipIcon[$c['kind']]; ?></span> <?php echo $this->escape($c['label']); ?></li>
										<?php } ?>
									</ul>
								<?php } ?>
							</div>
						</div>
						<?php if ($editable) { ?>
						<div class="mm-count"><span class="mm-preview"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></span></div>
						<?php } ?>

						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_FROM'); ?></span>
							<input class="mm-from-info" type="text" value="<?php echo $this->escape($fromShown); ?>" readonly title="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_FROM_INFO_HINT')); ?>" />
						</div>
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></span>
							<?php if ($isSuper && $editable) { ?>
								<input type="text" name="fields[reply_to]" id="field-reply-to" value="<?php echo $this->escape($replyTo); ?>" />
							<?php } else { ?>
								<span class="mm-mail-value"><?php echo $replyTo ? $this->escape($replyTo) : '&mdash;'; ?></span>
							<?php } ?>
						</div>
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_SUBJECT'); ?></span>
							<?php if ($editable) { ?>
								<input class="mm-mail-subject<?php echo $badSubject ? ' mm-invalid' : ''; ?>" type="text" name="fields[subject]" id="field-subject" value="<?php echo $this->escape($this->row->get('subject')); ?>" />
							<?php } else { ?>
								<span class="mm-mail-value mm-mail-subject-ro"><?php echo $this->escape(stripslashes($this->row->get('subject'))); ?></span>
							<?php } ?>
						</div>
					</div>

					<?php if ($editable) { ?>
					<div class="mm-mail-body<?php echo $badBody ? ' mm-invalid' : ''; ?>">
						<?php
						$editor = \Hubzero\Html\Editor::getInstance('ckeditor5');
						echo $editor->display(
							'fields[body]',
							$this->row->get('body'),
							'100%', '350', 35, 12, false, 'field-body', null, null,
							array(
								'toolbar' => array('bold', 'italic', 'link', 'bulletedList', 'numberedList', '|', 'undo', 'redo')
							)
						);
						?>
						<input type="file" name="attachment[]" id="field-attachment" multiple="multiple" hidden />
						<ul class="mm-chips" id="mm-chips">
							<?php foreach ($atts as $att) { ?>
								<li class="mm-chip">
									<span class="mm-clip">&#128206;</span> <?php echo $this->escape($att->get('filename')); ?>
									<span class="mm-kb">(<?php echo round($att->get('size') / 1024) . ' KB'; ?>)</span>
									<?php if (!$isNew) { ?>
										<a class="mm-chip-x" title="<?php echo $this->escape(Lang::txt('JACTION_DELETE')); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeattachment&attachment=' . $att->get('id') . '&' . Session::getFormToken() . '=1'); ?>">&times;</a>
									<?php } ?>
								</li>
							<?php } ?>
						</ul>
					</div>

					<p class="mm-header-hint"><?php echo Lang::txt($isSuper ? 'COM_MASSMAIL_SENDER_OPTIONS_HINT' : 'COM_MASSMAIL_SENDER_LOCKED_HINT'); ?></p>
					<?php } else { ?>
					<div class="mm-mail-body-ro">
						<iframe class="mm-message-body" sandbox="" title="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_MESSAGE_HEADING')); ?>" srcdoc="<?php echo $this->escape($this->previewHtml); ?>"></iframe>
						<?php if (count($atts)) { ?>
						<ul class="mm-chips">
							<?php foreach ($atts as $att) { ?>
								<li class="mm-chip"><span class="mm-clip">&#128206;</span> <?php echo $this->escape($att->get('filename')); ?> <span class="mm-kb">(<?php echo round($att->get('size') / 1024) . ' KB'; ?>)</span></li>
							<?php } ?>
						</ul>
						<?php } ?>
					</div>
					<?php } ?>
				</div>

				<?php if ($editable) { ?>
				<div class="mm-compose-actions">
					<?php if ($canDo->get('core.send')) { ?>
						<button type="button" class="btn mm-btn-send mm-submit" data-task="review"><?php echo $icSend . Lang::txt('COM_MASSMAIL_LAUNCH'); ?></button>
					<?php } ?>
					<span class="mm-spacer"></span>
					<button type="button" class="btn mm-btn-neutral mm-submit" data-task="save"><?php echo $icSave . Lang::txt('COM_MASSMAIL_SAVE_DRAFT'); ?></button>
					<?php if (!$isNew) { ?>
						<a class="btn mm-btn-danger mm-confirm" data-confirm="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_CONFIRM_DISCARD')); ?>" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=discard&id=' . (int) $this->row->get('id') . '&' . Session::getFormToken() . '=1'); ?>"><?php echo $icTrash . Lang::txt('COM_MASSMAIL_DISCARD'); ?></a>
					<?php } ?>
				</div>
				<?php } ?>
			</div>

			<div class="mm-side">
				<?php if (!$isNew) { ?>
				<div class="mm-card mm-status-card">
					<h4><?php echo Lang::txt('COM_MASSMAIL_DETAILS'); ?></h4>

					<?php
					// Meta block, using the admin template's shaded table.meta styling
					// (as com_content etc. do) for a consistent look. Rows appear only
					// when their value exists, so it grows over the mailing's lifecycle.
					$creatorName = $this->row->creator ? $this->row->creator->get('name') : '';
					$fmt = 'M j, Y g:i a';
					?>
					<table class="meta mm-meta">
						<tbody>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_FIELD_ID'); ?></th>
								<td><?php echo (int) $this->row->get('id'); ?></td>
							</tr>
							<tr>
								<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_STATUS'); ?></th>
								<td><span class="mm-status mm-status-<?php echo $status; ?>"><?php echo Lang::txt('COM_MASSMAIL_STATUS_' . strtoupper($status)); ?></span></td>
							</tr>
							<?php if ($this->row->get('created') && $this->row->get('created') != '0000-00-00 00:00:00') { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_CREATED'); ?></th>
									<td><?php echo Date::of($this->row->get('created'))->toLocal($fmt); ?></td>
								</tr>
							<?php } ?>
							<?php if ($creatorName) { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_CREATED_BY'); ?></th>
									<td><?php echo $this->escape($creatorName); ?></td>
								</tr>
							<?php } ?>
							<?php if ($this->row->get('modified') && $this->row->get('modified') != '0000-00-00 00:00:00') { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_MODIFIED'); ?></th>
									<td><?php echo Date::of($this->row->get('modified'))->toLocal($fmt); if ($this->row->get('modified_by')) { echo '<br /><span class="mm-meta-sub">' . Lang::txt('COM_MASSMAIL_META_BY', $this->escape(User::getInstance($this->row->get('modified_by'))->get('name'))) . '</span>'; } ?></td>
								</tr>
							<?php } ?>
							<?php if ($status == constant($Mailing . '::STATUS_SCHEDULED') && $this->row->get('scheduled_at')) { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_SCHEDULED'); ?></th>
									<td><?php echo Date::of($this->row->get('scheduled_at'))->toLocal($fmt . ' T'); ?></td>
								</tr>
							<?php } ?>
							<?php if ($this->row->get('approved_by')) { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_APPROVED_BY'); ?></th>
									<td><?php echo $this->escape(User::getInstance($this->row->get('approved_by'))->get('name')); if ($this->row->get('approved_on')) { echo '<br /><span class="mm-meta-sub">' . Date::of($this->row->get('approved_on'))->toLocal($fmt) . '</span>'; } ?></td>
								</tr>
							<?php } ?>
							<?php if ($this->row->get('started_on') && $this->row->get('started_on') != '0000-00-00 00:00:00') { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_STARTED'); ?></th>
									<td><?php echo Date::of($this->row->get('started_on'))->toLocal($fmt); ?></td>
								</tr>
							<?php } ?>
							<?php if ($this->row->get('completed_on') && $this->row->get('completed_on') != '0000-00-00 00:00:00') { ?>
								<tr>
									<th scope="row"><?php echo Lang::txt('COM_MASSMAIL_META_COMPLETED'); ?></th>
									<td><?php echo Date::of($this->row->get('completed_on'))->toLocal($fmt); ?></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>

					<?php // Admin status controls, shown as appropriate to the current state. ?>
					<div class="mm-status-actions">
						<?php // Pull a held/pending/scheduled mailing back to draft to re-edit.
						      // It must be re-launched after, which re-runs the send gates. ?>
						<?php if ($this->row->canReturnToDraft() && ($canDo->get('core.edit') || $canDo->get('core.create'))) { ?>
							<a class="btn mm-edit-draft" href="<?php echo Route::url($base . '&task=todraft&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icEdit . Lang::txt('COM_MASSMAIL_EDIT_DRAFT'); ?></a>
						<?php } ?>
						<?php if ($status == constant($Mailing . '::STATUS_PENDING_APPROVAL') && $canDo->get('core.approve')) { ?>
							<a class="btn btn-success" href="<?php echo Route::url($base . '&task=approve&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icCheck . Lang::txt('COM_MASSMAIL_APPROVE'); ?></a>
						<?php } ?>
						<?php if ($status == constant($Mailing . '::STATUS_HELD') && $canDo->get('core.release')) { ?>
							<a class="btn btn-success" href="<?php echo Route::url($base . '&task=release&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icSend . Lang::txt('COM_MASSMAIL_RELEASE'); ?></a>
						<?php } ?>
						<?php if (in_array($status, array(constant($Mailing . '::STATUS_SENDING'), constant($Mailing . '::STATUS_PREPARING'))) && $canDo->get('core.edit.state')) { ?>
							<a class="btn" href="<?php echo Route::url($base . '&task=pause&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icPause . Lang::txt('COM_MASSMAIL_PAUSE'); ?></a>
						<?php } ?>
						<?php if ($status == constant($Mailing . '::STATUS_PAUSED') && $canDo->get('core.edit.state')) { ?>
							<a class="btn" href="<?php echo Route::url($base . '&task=resume&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icPlay . Lang::txt('COM_MASSMAIL_RESUME'); ?></a>
						<?php } ?>
						<?php if (in_array($status, array(constant($Mailing . '::STATUS_SENDING'), constant($Mailing . '::STATUS_PREPARING'), constant($Mailing . '::STATUS_PAUSED'), constant($Mailing . '::STATUS_PENDING_APPROVAL'), constant($Mailing . '::STATUS_HELD'), constant($Mailing . '::STATUS_SCHEDULED'))) && $canDo->get('core.edit.state')) { ?>
							<a class="btn btn-danger mm-confirm" data-confirm="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_CONFIRM_CANCEL')); ?>" href="<?php echo Route::url($base . '&task=stop&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icX . Lang::txt('COM_MASSMAIL_CANCEL_MAILING'); ?></a>
						<?php } ?>
						<?php // Permanent delete -- only for mailings that never started sending
						      // (a sent/in-flight mailing keeps its record and can only be
						      // cancelled). On an editable draft this is omitted: the compose
						      // bar's "Discard" does the same thing (both purge the mailing),
						      // so we only surface Delete on the read-only deletable states
						      // (held / pending / scheduled) which have no Discard button. ?>
						<?php if (!$editable && $this->row->isDeletable() && $canDo->get('core.delete')) { ?>
							<a class="btn btn-danger mm-confirm" data-confirm="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_CONFIRM_DELETE_ONE')); ?>" href="<?php echo Route::url($base . '&task=delete&id=' . $this->row->get('id') . '&' . $token . '=1'); ?>"><?php echo $icTrash . Lang::txt('COM_MASSMAIL_DELETE_MAILING'); ?></a>
						<?php } ?>
					</div>

					<?php if ($total > 0 || $this->row->get('started_on')) { ?>
						<div class="mm-progress-bar"><div class="mm-progress-fill" data-pct="<?php echo $pct; ?>"></div></div>
						<table class="mm-status-stats">
							<tbody>
								<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_TOTAL_RECIPIENTS'); ?></th><td><?php echo number_format($total); ?></td></tr>
								<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_SENT'); ?></th><td><?php echo number_format($sentN); ?></td></tr>
								<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_PENDING'); ?></th><td><?php echo number_format($pendingN); ?></td></tr>
								<tr><th scope="row"><?php echo Lang::txt('COM_MASSMAIL_FAILED'); ?></th><td><?php echo number_format($failedN); ?></td></tr>
							</tbody>
						</table>
					<?php } ?>
				</div>
				<?php } ?>

				<?php if ($editable) { ?>
				<div class="mm-card">
					<h4><?php echo Lang::txt('COM_MASSMAIL_DELIVERY'); ?></h4>
					<div class="mm-delivery-toggle">
						<label class="mm-dopt<?php echo $isScheduled ? '' : ' is-active'; ?>">
							<input type="radio" name="delivery_mode" value="now"<?php echo $isScheduled ? '' : ' checked="checked"'; ?> />
							<span class="mm-dopt-body">
								<span class="mm-dopt-title"><?php echo Lang::txt('COM_MASSMAIL_DELIVER_NOW'); ?></span>
								<span class="mm-dopt-sub"><?php echo Lang::txt('COM_MASSMAIL_DELIVER_NOW_SUB'); ?></span>
							</span>
						</label>
						<label class="mm-dopt<?php echo $isScheduled ? ' is-active' : ''; ?>">
							<input type="radio" name="delivery_mode" value="schedule"<?php echo $isScheduled ? ' checked="checked"' : ''; ?> />
							<span class="mm-dopt-body">
								<span class="mm-dopt-title"><?php echo Lang::txt('COM_MASSMAIL_DELIVER_SCHEDULE'); ?></span>
								<span class="mm-dopt-sub"><?php echo Lang::txt('COM_MASSMAIL_DELIVER_SCHEDULE_SUB'); ?></span>
							</span>
						</label>
					</div>
					<div class="mm-schedule-field"<?php echo $isScheduled ? '' : ' hidden'; ?>>
						<?php echo Html::input('calendar', 'scheduled_at', $scheduledLocal, array('id' => 'field-schedule', 'format' => 'Y-m-d H:i:s')); ?>
					</div>

				</div>
				<?php } ?>

				<?php // Mass mailing is off: a boxed notice (matching the review page's
				      // disposition style) shown for any not-yet-sent mailing -- both the
				      // editable draft and the read-only held/pending/scheduled views -- so
				      // the "off / will be held / needs release" state is always clear. ?>
				<?php if (!$sendingOn && ($editable || $this->row->canReturnToDraft())) { ?>
					<div class="mm-disp mm-disp-hold">
						<span class="mm-disp-title"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_HELD_TITLE'); ?></span>
						<span class="mm-disp-sub"><?php echo Lang::txt('COM_MASSMAIL_VERIFY_WILL_HOLD'); ?></span>
					</div>
				<?php } ?>

			</div>
		</div>


		<input type="hidden" name="rcpt_all" id="field-all" value="<?php echo $hidAll; ?>" />
		<input type="hidden" name="groups[]" id="field-groups" value="<?php echo $this->escape($hidGroups); ?>" />
		<input type="hidden" name="users" id="field-users" value="<?php echo $this->escape($hidUsers); ?>" />
		<input type="hidden" name="emails" id="field-emails" value="<?php echo $this->escape($hidEmails); ?>" />
		<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</form>

	<div id="mm-compose-config" hidden data-lang="<?php echo htmlspecialchars(json_encode($composeLang), ENT_QUOTES, 'UTF-8'); ?>" data-state="<?php echo htmlspecialchars(json_encode($rcptState), ENT_QUOTES, 'UTF-8'); ?>"></div>

	<?php if ($editable) { ?>
	<div class="mm-modal" id="mm-rcpt-modal" role="dialog" aria-modal="true" aria-label="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_RECIPIENTS')); ?>">
		<div class="mm-modal-inner mm-rcpt-inner">
			<button type="button" class="mm-modal-close" id="mm-rcpt-close" aria-label="<?php echo $this->escape(Lang::txt('JCLOSE')); ?>">&times;</button>
			<h3><?php echo Lang::txt('COM_MASSMAIL_RECIPIENTS'); ?></h3>
			<p class="mm-preview mm-modal-count"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></p>

			<div class="mm-rcpt-section">
				<label class="mm-everyone"><input type="checkbox" id="mb-all" /> <?php echo Lang::txt('COM_MASSMAIL_EVERYONE'); ?></label>
			</div>
			<div class="mm-rcpt-section">
				<h5><?php echo Lang::txt('COM_MASSMAIL_RCPT_GROUPS'); ?></h5>
				<ul class="mm-chips" id="mb-group-chips"></ul>
				<div class="mm-ac-wrap">
					<input type="text" id="mb-group-search" placeholder="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_GROUP_SEARCH')); ?>" autocomplete="off" spellcheck="false" readonly />
					<ul class="mm-ac-list" id="mb-group-ac" hidden></ul>
				</div>
			</div>
			<div class="mm-rcpt-section">
				<h5><?php echo Lang::txt('COM_MASSMAIL_RCPT_PEOPLE'); ?></h5>
				<ul class="mm-chips" id="mb-user-chips"></ul>
				<div class="mm-ac-wrap">
					<input type="text" id="mb-user-search" placeholder="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_PEOPLE_SEARCH')); ?>" autocomplete="off" spellcheck="false" readonly />
					<ul class="mm-ac-list" id="mb-user-ac" hidden></ul>
				</div>
			</div>
			<div class="mm-rcpt-section">
				<h5><?php echo Lang::txt('COM_MASSMAIL_RCPT_EMAILS'); ?></h5>
				<textarea id="mb-emails" rows="3" placeholder="one per line"></textarea>
			</div>

			<?php if ($canDo->get('core.override')) { ?>
			<div class="mm-rcpt-section">
				<h5><?php echo Lang::txt('COM_MASSMAIL_COMPLIANCE'); ?></h5>
				<label class="mm-warn mm-optout-label"><input type="checkbox" form="adminForm" name="ignore_optout" id="field-optout" value="1"<?php echo ($ignore ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT'); ?></label>
				<p class="<?php echo $ignore ? 'warning' : 'info'; ?> mm-optout-note" id="mm-optout-note"><?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT_HINT'); ?></p>
			</div>
			<?php } ?>
			<div class="mm-modal-actions">
				<button type="button" class="btn btn-primary" id="mb-done"><?php echo Lang::txt('COM_MASSMAIL_DONE'); ?></button>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

