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

// The mail server must be configured to send as the From address, so only a
// Super Administrator may edit the sender; everyone else sees it read-only.
$isSuper = User::authorise('core.admin');

$fromName  = trim((string) $this->row->get('from_name'));
$fromEmail = trim((string) $this->row->get('from_email'));
$replyTo   = trim((string) $this->row->get('reply_to'));
$fromShown = $fromEmail ? ($fromName ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail) : $fromName;

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

Toolbar::title(Lang::txt($isNew ? 'COM_MASSMAIL_NEW_MAILING' : 'COM_MASSMAIL_EDIT_MAILING'), 'massmail');
Toolbar::apply();
Toolbar::save();
if (!$isNew && $canDo->get('core.send'))
{
	Toolbar::custom('sendtest', 'preview', 'preview', Lang::txt('COM_MASSMAIL_SEND_TEST'), false);
	Toolbar::custom('launch', 'upload', 'upload', Lang::txt('COM_MASSMAIL_LAUNCH'), false);
}
Toolbar::cancel();
?>
<style>
/* Compose page: bounded width, centered, with gutters. */
.mm-compose { max-width:1080px; margin:14px auto 0; padding:0 20px; box-sizing:border-box; }
.mm-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
@media (max-width:960px){ .mm-grid{ grid-template-columns:1fr; } }
.mm-compose input::placeholder, .mm-compose textarea::placeholder { color:#b7c0cb; opacity:1; }
.mm-compose .hint { color:#a7b0bd; }

.mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }

/* Webmail-style message card. */
.mm-mail { overflow:visible; }
.mm-mail-headers { padding:2px 18px; }
.mm-mail-row { display:flex; align-items:flex-start; gap:12px; border-bottom:1px solid #edf2f7; padding:10px 0; }
.mm-mail-label { flex:0 0 68px; color:#718096; font-size:13px; padding-top:3px; }
.mm-mail-value { color:#1a202c; font-size:14px; font-weight:600; }
.mm-mail-row > input { flex:1; border:none; background:transparent; box-shadow:none; padding:2px 0; margin:0; font-size:14px; color:#1a202c; min-width:0; }
.mm-mail-row > input:focus { outline:none; box-shadow:none; }
.mm-mail-subject { font-weight:600; }
.mm-mail-from { display:flex; gap:10px; flex:1; min-width:0; }
.mm-mail-from input.mm-from-name { flex:0 0 38%; }
.mm-mail-body { padding:14px 18px 18px; }
.mm-header-hint { margin:0; padding:0 18px 14px; color:#a0aec0; font-size:12px; }

/* To: read-only summary + edit button. */
.mm-to-display { flex:1; display:flex; flex-wrap:wrap; align-items:center; gap:6px; min-width:0; }
.mm-to-chips { display:flex; flex-wrap:wrap; gap:6px; list-style:none; margin:0; padding:0; }
.mm-to-edit { background:none; border:1px dashed #cbd5e0; border-radius:14px; color:#3182ce; font-size:12px; padding:3px 12px; cursor:pointer; }
.mm-to-edit:hover { border-color:#3182ce; background:#ebf4ff; }
.mm-to-none { color:#a0aec0; font-size:13px; }
.mm-count { color:#718096; font-size:12px; padding:4px 0 2px 80px; }
.mm-count strong { color:#2d3748; }

/* Recipient builder modal. */
.mm-modal { position:fixed; inset:0; background:rgba(15,23,42,.55); display:none; z-index:10000; align-items:flex-start; justify-content:center; padding:40px 20px; overflow:auto; }
.mm-modal.open { display:flex; }
.mm-modal-inner { background:#fff; border-radius:10px; padding:22px 24px; width:100%; max-width:520px; position:relative; box-shadow:0 12px 40px rgba(0,0,0,.3); }
.mm-modal-close { position:absolute; top:8px; right:14px; font-size:26px; line-height:1; background:none; border:none; color:#718096; cursor:pointer; }
.mm-rcpt-inner h3 { margin:0 0 4px; font-size:18px; }
.mm-modal-count { margin:0 0 16px; color:#718096; }
.mm-rcpt-section { padding:14px 0; border-top:1px solid #edf2f7; }
.mm-rcpt-section:first-of-type { border-top:none; padding-top:2px; }
.mm-rcpt-section h5 { margin:0 0 8px; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#718096; font-weight:700; }
.mm-rcpt-section textarea { width:100%; box-sizing:border-box; }
.mm-rcpt-section .mm-chips { margin:0; }
.mm-rcpt-section .mm-chips:not(:empty) { margin:0 0 10px; }
.mm-modal-actions { margin-top:18px; text-align:right; }

/* Chips (To: and attachments). */
.mm-chip { display:inline-flex; align-items:center; gap:6px; background:#edf2f7; border:1px solid #e2e8f0; border-radius:14px; padding:2px 6px 2px 9px; font-size:12px; color:#2d3748; }
.mm-chip .mm-chip-ic { font-size:11px; line-height:1; }
.mm-chip-all { background:#fef3c7; border-color:#fcd34d; }
.mm-chip-group { background:#e0e7ff; border-color:#c7d2fe; }
.mm-chip-user { background:#dcfce7; border-color:#bbf7d0; }
.mm-chip .mm-kb { color:#a0aec0; }
.mm-chip-x { background:none; border:none; color:#718096; cursor:pointer; font-size:15px; line-height:1; padding:0 2px; text-decoration:none; }
.mm-chip-x:hover { color:#c53030; }

/* Autocomplete dropdown. */
.mm-ac-wrap { position:relative; }
.mm-ac-list { position:absolute; z-index:60; left:0; right:0; top:100%; margin:2px 0 0; padding:0; list-style:none; background:#fff; border:1px solid #cbd5e0; border-radius:6px; box-shadow:0 4px 14px rgba(0,0,0,.14); max-height:240px; overflow:auto; min-width:240px; }
.mm-ac-list li { display:flex; align-items:baseline; gap:6px; padding:6px 10px; cursor:pointer; font-size:13px; border-bottom:1px solid #f1f5f9; }
.mm-ac-list li:last-child { border-bottom:none; }
.mm-ac-list li:hover, .mm-ac-list li.active { background:#ebf4ff; }
.mm-ac-ic { font-size:11px; }
.mm-ac-cn { font-weight:600; color:#2d3748; }
.mm-ac-title { color:#718096; font-size:12px; }

/* Framed editor. */
#adminForm .ck.ck-toolbar { border-radius:5px 5px 0 0; border-color:#ccc; background:#f7f8fa; }
#adminForm .ck.ck-editor__editable { border-color:#ccc; border-radius:0 0 5px 5px; }
#adminForm .ck.ck-editor__editable.ck-focused { border-color:#4299e1; box-shadow:none; }
.mm-attach-btn { cursor:pointer; }
.mm-attach-btn svg { pointer-events:none; }
.mm-chips { list-style:none; margin:10px 0 0; padding:0; display:flex; flex-wrap:wrap; gap:6px; }
.mm-chips:empty { margin:0; }

/* Sidebar cards. */
.mm-side .mm-card { padding:16px; margin-bottom:16px; }
.mm-side h4 { margin:0 0 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#718096; font-weight:700; }
.mm-optout-label { display:block; margin-bottom:14px; }
.mm-optout-note { margin-top:0; }
.mm-compose .input-datetime { display:block; }
.mm-compose .calendar-field { width:100%; box-sizing:border-box; }

/* Delivery-mode toggle (as-soon-as-possible vs scheduled). */
.mm-delivery-toggle { display:flex; flex-direction:column; gap:8px; }
.mm-dopt { display:flex; align-items:flex-start; gap:10px; margin:0; padding:10px 12px; border:1px solid #e2e8f0; border-radius:8px; cursor:pointer; transition:border-color .12s ease, background .12s ease; }
.mm-dopt:hover { border-color:#cbd5e0; }
.mm-dopt.is-active { border-color:#4299e1; background:#ebf4ff; }
.mm-dopt input { margin:2px 0 0; flex:0 0 auto; }
.mm-dopt-body { display:flex; flex-direction:column; line-height:1.3; }
.mm-dopt-title { font-weight:600; color:#2d3748; font-size:13px; }
.mm-dopt-sub { color:#a0aec0; font-size:12px; }
.mm-schedule-field { margin-top:10px; }
.mm-schedule-field[hidden] { display:none; }
</style>

<div class="mm-compose">

	<?php if (!$sendingOn) { ?>
	<p class="warning"><?php echo Lang::txt('COM_MASSMAIL_SENDING_OFF_WARNING'); ?></p>
	<?php } ?>

	<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="hz-form">
		<div class="mm-grid">
			<div class="mm-main">
				<div class="mm-card mm-mail">
					<div class="mm-mail-headers">
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_TO'); ?></span>
							<div class="mm-to-display" id="mm-to-display">
								<ul class="mm-to-chips" id="mm-to-summary"></ul>
								<button type="button" class="mm-to-edit" id="mm-to-edit"><?php echo Lang::txt('COM_MASSMAIL_EDIT_RECIPIENTS'); ?></button>
							</div>
						</div>
						<div class="mm-count"><span class="mm-preview"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></span></div>

						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_FROM'); ?></span>
							<?php if ($isSuper) { ?>
								<span class="mm-mail-from">
									<input class="mm-from-name" type="text" name="fields[from_name]" id="field-from-name" value="<?php echo $this->escape($fromName); ?>" placeholder="<?php echo $this->escape(Config::get('fromname')); ?>" />
									<input type="text" name="fields[from_email]" id="field-from-email" value="<?php echo $this->escape($fromEmail); ?>" placeholder="<?php echo $this->escape(Config::get('mailfrom')); ?>" />
								</span>
							<?php } else { ?>
								<span class="mm-mail-value"><?php echo $this->escape($fromShown); ?></span>
							<?php } ?>
						</div>
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></span>
							<?php if ($isSuper) { ?>
								<input type="text" name="fields[reply_to]" id="field-reply-to" value="<?php echo $this->escape($replyTo); ?>" />
							<?php } else { ?>
								<span class="mm-mail-value"><?php echo $replyTo ? $this->escape($replyTo) : '&mdash;'; ?></span>
							<?php } ?>
						</div>
						<div class="mm-mail-row">
							<span class="mm-mail-label"><?php echo Lang::txt('COM_MASSMAIL_SUBJECT'); ?></span>
							<input class="mm-mail-subject" type="text" name="fields[subject]" id="field-subject" value="<?php echo $this->escape($this->row->get('subject')); ?>" placeholder="<?php echo $this->escape(Lang::txt('COM_MASSMAIL_SUBJECT')); ?>" />
						</div>
					</div>

					<div class="mm-mail-body">
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

					<?php if (!$isSuper) { ?>
					<p class="mm-header-hint"><?php echo Lang::txt('COM_MASSMAIL_SENDER_LOCKED_HINT'); ?></p>
					<?php } ?>
				</div>
			</div>

			<div class="mm-side">
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
				<label class="mm-warn mm-optout-label"><input type="checkbox" name="ignore_optout" id="field-optout" value="1"<?php echo ($ignore ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT'); ?></label>
				<p class="<?php echo $ignore ? 'warning' : 'info'; ?> mm-optout-note" id="mm-optout-note"><?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT_HINT'); ?></p>
			</div>
			<?php } ?>
			<div class="mm-modal-actions">
				<button type="button" class="btn btn-primary" id="mb-done"><?php echo Lang::txt('COM_MASSMAIL_DONE'); ?></button>
			</div>
		</div>
	</div>
</div>
<?php $rcptState = array(
	'all'    => !empty($spec['all']),
	'groups' => array_map(function ($g) { return array('token' => (string) $g['token'], 'label' => $g['label']); }, $gChips),
	'users'  => array_map(function ($u) { return array('token' => (string) $u['token'], 'label' => $u['label']); }, $uChips),
	'emails' => array_values($emails),
); ?>

<script type="text/javascript">
(function () {
	var form = document.getElementById('adminForm');

	// --- Live audience prediction (reads the hidden recipient fields) --------
	var outs  = document.querySelectorAll('.mm-preview strong');
	var timer = null;
	function collect() {
		var fd = new FormData();
		if (document.getElementById('field-all').value === '1') { fd.append('rcpt_all', '1'); }
		fd.append('groups[]', document.getElementById('field-groups').value);
		fd.append('users', document.getElementById('field-users').value);
		fd.append('emails', document.getElementById('field-emails').value);
		var ig = document.querySelector('input[name="ignore_optout"]');
		if (ig && ig.checked) { fd.append('ignore_optout', '1'); }
		return fd;
	}
	function update() {
		if (!form || !outs.length) { return; }
		fetch(form.getAttribute('action') + '&task=preview', {
			method: 'POST', body: collect(), credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
		.then(function (r) { return r.json(); })
		.then(function (d) { if (d && typeof d.text === 'string') { for (var oi = 0; oi < outs.length; oi++) { outs[oi].textContent = d.text; } } })
		.catch(function () {});
	}
	function schedule() { clearTimeout(timer); timer = setTimeout(update, 300); }

	// --- Recipient builder modal ---------------------------------------------
	(function () {
		var modal   = document.getElementById('mm-rcpt-modal');
		var summary = document.getElementById('mm-to-summary');
		if (!modal || !summary) { return; }
		var ICON = { all: '★', group: '👥', user: '👤', email: '✉' };
		var EVERYONE = <?php echo json_encode(Lang::txt('COM_MASSMAIL_EVERYONE')); ?>;
		var NONE = <?php echo json_encode(Lang::txt('COM_MASSMAIL_NO_RECIPIENTS_YET')); ?>;
		var EDIT_LABEL = <?php echo json_encode(Lang::txt('COM_MASSMAIL_EDIT_RECIPIENTS')); ?>;
		var ADD_LABEL = <?php echo json_encode(Lang::txt('COM_MASSMAIL_ADD_RECIPIENTS')); ?>;
		var state = <?php echo json_encode($rcptState); ?>;

		var fAll = document.getElementById('field-all');
		var fGroups = document.getElementById('field-groups');
		var fUsers = document.getElementById('field-users');
		var fEmails = document.getElementById('field-emails');

		function serialize() {
			fAll.value = state.all ? '1' : '0';
			fGroups.value = state.groups.map(function (g) { return g.token; }).join(',');
			fUsers.value  = state.users.map(function (u) { return u.token; }).join(',');
			fEmails.value = state.emails.join(',');
			schedule();
		}
		function makeChip(kind, label, onRemove) {
			var li = document.createElement('li');
			li.className = 'mm-chip mm-chip-' + kind;
			var ic = document.createElement('span'); ic.className = 'mm-chip-ic'; ic.textContent = ICON[kind];
			var s = document.createElement('span'); s.textContent = label;
			li.appendChild(ic); li.appendChild(s);
			if (onRemove) {
				var x = document.createElement('button'); x.type = 'button'; x.className = 'mm-chip-x'; x.innerHTML = '&times;';
				x.addEventListener('click', onRemove); li.appendChild(x);
			}
			return li;
		}
		function renderSummary() {
			summary.innerHTML = '';
			var any = false;
			if (state.all) { summary.appendChild(makeChip('all', EVERYONE)); any = true; }
			state.groups.forEach(function (g) { summary.appendChild(makeChip('group', g.label)); any = true; });
			state.users.forEach(function (u) { summary.appendChild(makeChip('user', u.label)); any = true; });
			state.emails.forEach(function (e) { summary.appendChild(makeChip('email', e)); any = true; });
			if (!any) { var none = document.createElement('li'); none.className = 'mm-to-none'; none.textContent = NONE; summary.appendChild(none); }
			var eb = document.getElementById('mm-to-edit'); if (eb) { eb.textContent = any ? EDIT_LABEL : ADD_LABEL; }
		}

		var mbAll = document.getElementById('mb-all');
		var mbGroupChips = document.getElementById('mb-group-chips');
		var mbUserChips = document.getElementById('mb-user-chips');
		var mbEmails = document.getElementById('mb-emails');

		function renderModal() {
			mbAll.checked = !!state.all;
			mbGroupChips.innerHTML = '';
			state.groups.forEach(function (g, i) { mbGroupChips.appendChild(makeChip('group', g.label, function () { state.groups.splice(i, 1); changed(); })); });
			mbUserChips.innerHTML = '';
			state.users.forEach(function (u, i) { mbUserChips.appendChild(makeChip('user', u.label, function () { state.users.splice(i, 1); changed(); })); });
			if (document.activeElement !== mbEmails) { mbEmails.value = state.emails.join('\n'); }
		}
		function changed() { serialize(); renderSummary(); renderModal(); }

		function sectionPicker(searchId, acId, task, listKey, tokenIsId) {
			var search = document.getElementById(searchId);
			var ac = document.getElementById(acId);
			if (!search || !ac) { return; }
			var t = null, items = [], active = -1;
			function hide() { ac.hidden = true; ac.innerHTML = ''; items = []; active = -1; }
			function has(token) { return state[listKey].some(function (e) { return e.token === ('' + token); }); }
			function render(list) {
				items = list; active = -1;
				if (!list.length) { hide(); return; }
				ac.innerHTML = '';
				list.forEach(function (it) {
					var li = document.createElement('li');
					var nm = document.createElement('span'); nm.className = 'mm-ac-cn'; nm.textContent = (it.title || it.cn);
					var subv = it.cn || '';
					var sub = document.createElement('span'); sub.className = 'mm-ac-title'; sub.textContent = subv ? (' — ' + subv) : '';
					li.appendChild(nm); li.appendChild(sub);
					li.addEventListener('mousedown', function (e) { e.preventDefault(); addOne(it); });
					ac.appendChild(li);
				});
				ac.hidden = false;
			}
			function addOne(it) {
				var token = '' + (tokenIsId ? it.id : it.cn);
				if (!has(token)) { state[listKey].push({ token: token, label: (it.title || it.cn) }); changed(); }
				search.value = ''; hide(); search.focus();
			}
			function lookup() {
				var q = search.value.trim();
				if (q.length < 2) { hide(); return; }
				fetch(form.getAttribute('action') + '&task=' + task + '&q=' + encodeURIComponent(q), { credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' } })
					.then(function (r) { return r.json(); }).then(render).catch(hide);
			}
			search.addEventListener('focus', function () { search.removeAttribute('readonly'); });
			search.addEventListener('input', function () { clearTimeout(t); t = setTimeout(lookup, 180); });
			search.addEventListener('keydown', function (e) {
				if (ac.hidden) { return; }
				var lis = ac.querySelectorAll('li');
				if (e.key === 'ArrowDown') { e.preventDefault(); active = Math.min(active + 1, lis.length - 1); }
				else if (e.key === 'ArrowUp') { e.preventDefault(); active = Math.max(active - 1, 0); }
				else if (e.key === 'Enter' && active >= 0) { e.preventDefault(); addOne(items[active]); return; }
				else if (e.key === 'Escape') { hide(); return; }
				else { return; }
				for (var i = 0; i < lis.length; i++) { lis[i].classList.toggle('active', i === active); }
			});
			search.addEventListener('blur', function () { setTimeout(hide, 150); });
		}
		sectionPicker('mb-group-search', 'mb-group-ac', 'grouplookup', 'groups', false);
		sectionPicker('mb-user-search', 'mb-user-ac', 'userlookup', 'users', true);

		mbAll.addEventListener('change', function () { state.all = mbAll.checked; changed(); });
		mbEmails.addEventListener('input', function () {
			var arr = [];
			mbEmails.value.split(/[\s,;]+/).forEach(function (e) { e = e.trim(); if (e !== '') { arr.push(e); } });
			state.emails = arr; serialize(); renderSummary();
		});

		var editBtn = document.getElementById('mm-to-edit');
		var closeBtn = document.getElementById('mm-rcpt-close');
		var doneBtn = document.getElementById('mb-done');
		function openModal() { renderModal(); modal.classList.add('open'); }
		function closeModal() { modal.classList.remove('open'); }
		if (editBtn) { editBtn.addEventListener('click', openModal); }
		if (closeBtn) { closeBtn.addEventListener('click', closeModal); }
		if (doneBtn) { doneBtn.addEventListener('click', closeModal); }
		modal.addEventListener('click', function (e) { if (e.target === modal) { closeModal(); } });
		document.addEventListener('keydown', function (e) { if ((e.key === 'Escape' || e.keyCode === 27) && modal.classList.contains('open')) { closeModal(); } });

		serialize(); renderSummary();
	})();

	// --- Opt-out note: informational until the override is actually enabled ---
	var ign = document.querySelector('input[name="ignore_optout"]');
	if (ign) { ign.addEventListener('change', schedule); }
	var note = document.getElementById('mm-optout-note');
	if (ign && note) {
		function syncNote() {
			note.classList.toggle('warning', ign.checked);
			note.classList.toggle('info', !ign.checked);
		}
		ign.addEventListener('change', syncNote);
		syncNote();
	}

	// --- Delivery mode: as-soon-as-possible vs scheduled ---------------------
	var dModes = document.querySelectorAll('input[name="delivery_mode"]');
	var schedWrap = document.querySelector('.mm-schedule-field');
	var schedInput = document.getElementById('field-schedule');
	if (dModes.length && schedWrap) {
		function reflectDelivery() {
			var m = document.querySelector('input[name="delivery_mode"]:checked');
			var isSched = !!m && m.value === 'schedule';
			schedWrap.hidden = !isSched;
			if (!isSched && schedInput) { schedInput.value = ''; }
			var opts = document.querySelectorAll('.mm-dopt');
			for (var i = 0; i < opts.length; i++) {
				var r = opts[i].querySelector('input');
				opts[i].classList.toggle('is-active', !!r && r.checked);
			}
			return isSched;
		}
		for (var d = 0; d < dModes.length; d++) {
			dModes[d].addEventListener('change', function () {
				if (reflectDelivery() && schedInput) { schedInput.focus(); }
			});
		}
		reflectDelivery();
	}
	if (schedInput) {
		var idt = schedInput.closest('.input-datetime');
		if (idt) {
			idt.addEventListener('click', function (e) {
				if (e.target !== schedInput) {
					schedInput.focus();
					if (window.jQuery) { try { window.jQuery(schedInput).datepicker('show'); } catch (err) {} }
				}
			});
		}
	}

	// --- Paperclip attach in the editor toolbar ------------------------------
	var fileInput = document.getElementById('field-attachment');
	var chips = document.getElementById('mm-chips');
	if (fileInput && chips && window.DataTransfer) {
		var bag = new DataTransfer();
		function esc(s) { var d = document.createElement('div'); d.textContent = s; return d.innerHTML; }
		function renderPicked() {
			var old = chips.querySelectorAll('.mm-chip-new');
			for (var k = 0; k < old.length; k++) { old[k].remove(); }
			Array.prototype.forEach.call(bag.files, function (f, idx) {
				var li = document.createElement('li');
				li.className = 'mm-chip mm-chip-new';
				li.innerHTML = '<span class="mm-clip">📎</span> ' + esc(f.name) + ' <span class="mm-kb">(' + Math.round(f.size / 1024) + ' KB)</span>';
				var x = document.createElement('button');
				x.type = 'button'; x.className = 'mm-chip-x'; x.innerHTML = '&times;';
				x.addEventListener('click', function () {
					var nb = new DataTransfer();
					Array.prototype.forEach.call(bag.files, function (g, m) { if (m !== idx) { nb.items.add(g); } });
					bag = nb; fileInput.files = bag.files; renderPicked();
				});
				li.appendChild(x);
				chips.appendChild(li);
			});
		}
		fileInput.addEventListener('change', function () {
			Array.prototype.forEach.call(fileInput.files, function (f) { bag.items.add(f); });
			fileInput.files = bag.files;
			renderPicked();
		});

		var tries = 0;
		var iv = setInterval(function () {
			var items = document.querySelector('#adminForm .ck-toolbar__items') || document.querySelector('#adminForm .ck-toolbar');
			if (items && !document.getElementById('mm-attach-btn')) {
				var b = document.createElement('button');
				b.type = 'button'; b.id = 'mm-attach-btn';
				b.className = 'ck ck-button ck-off mm-attach-btn';
				b.title = <?php echo json_encode(Lang::txt('COM_MASSMAIL_ADD_ATTACHMENT')); ?>;
				b.setAttribute('aria-label', b.title);
				b.innerHTML = '<svg class="ck ck-icon ck-button__icon" viewBox="0 0 24 24"><path d="M16.5 6v11.5c0 2.21-1.79 4-4 4s-4-1.79-4-4V5c0-1.38 1.12-2.5 2.5-2.5s2.5 1.12 2.5 2.5v10.5c0 .55-.45 1-1 1s-1-.45-1-1V6H10v9.5c0 1.38 1.12 2.5 2.5 2.5s2.5-1.12 2.5-2.5V5c0-2.21-1.79-4-4-4S7 2.79 7 5v12.5c0 3.04 2.46 5.5 5.5 5.5s5.5-2.46 5.5-5.5V6h-1.5z"/></svg>';
				b.addEventListener('click', function (e) { e.preventDefault(); fileInput.click(); });
				items.appendChild(b);
				clearInterval(iv);
			}
			if (++tries > 40) { clearInterval(iv); }
		}, 250);
	}
})();
</script>
