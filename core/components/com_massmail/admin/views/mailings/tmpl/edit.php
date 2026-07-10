<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Massmail\Admin\Helpers\Permissions::getActions();

$isNew = $this->row->isNew();
$spec  = \Components\Massmail\Helpers\Recipients::normalizeSpec(json_decode((string) $this->row->get('recipient_spec'), true));
// A brand-new mailing defaults to "everyone" so the preview isn't 0 on open.
if ($isNew && empty($spec['groups']) && empty($spec['users']) && empty($spec['emails']))
{
	$spec['all'] = true;
}
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

// Live audience preview
$preview = \Components\Massmail\Helpers\Recipients::previewCount($spec, $ignore ? true : false);

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
.mm-compose input::placeholder, .mm-compose textarea::placeholder { color:#b7c0cb; opacity:1; }
.mm-compose .hint { color:#a7b0bd; }
.mm-compose p.warning { margin:0 0 16px; }
.mm-grid { display:grid; grid-template-columns:1fr 320px; gap:20px; align-items:start; }
@media (max-width:960px){ .mm-grid{ grid-template-columns:1fr; } }

.mm-card { background:#fff; border:1px solid #e2e8f0; border-radius:10px; }

/* Webmail-style message card. */
.mm-mail { overflow:hidden; }
.mm-mail-headers { padding:2px 18px; }
.mm-mail-row { display:flex; align-items:center; gap:12px; border-bottom:1px solid #edf2f7; padding:10px 0; }
.mm-mail-label { flex:0 0 68px; color:#718096; font-size:13px; }
.mm-mail-value { color:#1a202c; font-size:14px; font-weight:600; }
.mm-mail-row input { flex:1; border:none; background:transparent; box-shadow:none; padding:2px 0; margin:0; font-size:14px; color:#1a202c; min-width:0; }
.mm-mail-row input:focus { outline:none; box-shadow:none; }
.mm-mail-subject { font-weight:600; }
.mm-mail-from { display:flex; gap:10px; flex:1; min-width:0; }
.mm-mail-from input.mm-from-name { flex:0 0 38%; }
.mm-mail-body { padding:14px 18px 18px; }
.mm-header-hint { margin:0; padding:0 18px 14px; color:#a0aec0; font-size:12px; }

/* Sidebar cards. */
.mm-side .mm-card { padding:16px; margin-bottom:16px; }
.mm-side h4 { margin:0 0 12px; font-size:12px; text-transform:uppercase; letter-spacing:.04em; color:#718096; font-weight:700; }
.mm-preview { margin:0 0 12px; }
.mm-compose .input-datetime { display:block; }
.mm-compose .calendar-field { width:100%; box-sizing:border-box; }

/* Delivery-mode toggle (as-soon-as-possible vs scheduled) */
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
.mm-everyone { display:flex; align-items:center; gap:8px; margin:0 0 12px; font-weight:600; color:#2d3748; }
.mm-rcpt-hint { margin:0 0 12px; }
.mm-rcpt-sources.is-dim { opacity:.4; pointer-events:none; }
.mm-optout-label { display:block; margin-bottom:14px; }
.mm-optout-note { margin-top:0; }

/* Group alias autocomplete */
.mm-ac-wrap { position:relative; }
.mm-ac-list { position:absolute; z-index:60; left:0; right:0; top:100%; margin:2px 0 0; padding:0; list-style:none; background:#fff; border:1px solid #cbd5e0; border-radius:6px; box-shadow:0 4px 14px rgba(0,0,0,.14); max-height:220px; overflow:auto; }
.mm-ac-list li { padding:6px 10px; cursor:pointer; font-size:13px; border-bottom:1px solid #f1f5f9; }
.mm-ac-list li:last-child { border-bottom:none; }
.mm-ac-list li:hover, .mm-ac-list li.active { background:#ebf4ff; }
.mm-ac-cn { font-weight:600; color:#2d3748; }
.mm-ac-title { color:#718096; font-size:12px; }

/* Framed editor. */
#adminForm .ck.ck-toolbar { border-radius:5px 5px 0 0; border-color:#ccc; background:#f7f8fa; }
#adminForm .ck.ck-editor__editable { border-color:#ccc; border-radius:0 0 5px 5px; }
#adminForm .ck.ck-editor__editable.ck-focused { border-color:#4299e1; box-shadow:none; }
.mm-attach-btn { cursor:pointer; }
.mm-attach-btn svg { pointer-events:none; }

/* Attachment chips. */
.mm-chips { list-style:none; margin:10px 0 0; padding:0; display:flex; flex-wrap:wrap; gap:6px; }
.mm-chips:empty { margin:0; }
.mm-chip { display:inline-flex; align-items:center; gap:6px; background:#edf2f7; border:1px solid #e2e8f0; border-radius:14px; padding:3px 6px 3px 10px; font-size:12px; color:#2d3748; }
.mm-chip .mm-kb { color:#a0aec0; }
.mm-chip-x { background:none; border:none; color:#718096; cursor:pointer; font-size:15px; line-height:1; padding:0 2px; text-decoration:none; }
.mm-chip-x:hover { color:#c53030; }
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
								// Basic HTML formatting; the paperclip is injected after these.
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
					<h4><?php echo Lang::txt('COM_MASSMAIL_RECIPIENTS'); ?></h4>

					<p class="mm-preview"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></p>
					<p class="hint mm-rcpt-hint"><?php echo Lang::txt('COM_MASSMAIL_RCPT_COMBINE_HINT'); ?></p>

					<label class="mm-everyone">
						<input type="checkbox" name="rcpt_all" id="field-all" value="1"<?php echo !empty($spec['all']) ? ' checked="checked"' : ''; ?> />
						<?php echo Lang::txt('COM_MASSMAIL_RCPT_ALL'); ?>
					</label>

					<div class="mm-rcpt-sources" id="mm-rcpt-sources">
						<div class="input-wrap">
							<label for="field-groups"><?php echo Lang::txt('COM_MASSMAIL_GROUP_IDS'); ?></label>
							<div class="mm-ac-wrap">
								<input type="text" name="groups[]" id="field-groups" value="<?php echo $this->escape(implode(', ', (array) $spec['groups'])); ?>" placeholder="e.g. composites-team, 3042" autocomplete="off" />
								<ul class="mm-ac-list" id="mm-group-ac" hidden></ul>
							</div>
						</div>
						<div class="input-wrap">
							<label for="field-users"><?php echo Lang::txt('COM_MASSMAIL_USER_IDS'); ?></label>
							<div class="mm-ac-wrap">
								<input type="text" name="users" id="field-users" value="<?php echo $this->escape(implode(', ', (array) $spec['users'])); ?>" placeholder="e.g. jdoe, 1002" autocomplete="off" />
								<ul class="mm-ac-list" id="mm-user-ac" hidden></ul>
							</div>
						</div>
					</div>

					<div class="input-wrap">
						<label for="field-emails"><?php echo Lang::txt('COM_MASSMAIL_RAW_EMAILS'); ?></label>
						<textarea name="emails" id="field-emails" rows="4" placeholder="one per line"><?php echo $this->escape(implode("\n", (array) $spec['emails'])); ?></textarea>
					</div>
				</div>

				<?php $isScheduled = ($scheduledLocal !== ''); ?>
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

				<?php if ($canDo->get('core.override')) { ?>
				<div class="mm-card">
					<h4><?php echo Lang::txt('COM_MASSMAIL_COMPLIANCE'); ?></h4>
					<label class="mm-warn mm-optout-label">
						<input type="checkbox" name="ignore_optout" id="field-optout" value="1"<?php echo ($ignore ? ' checked="checked"' : ''); ?> />
						<?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT'); ?>
					</label>
					<p class="<?php echo $ignore ? 'warning' : 'info'; ?> mm-optout-note" id="mm-optout-note"><?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT_HINT'); ?></p>
				</div>
				<?php } ?>
			</div>
		</div>

		<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</form>
</div>

<script type="text/javascript">
(function () {
	// --- Live audience prediction (additive recipient list) ------------------
	var form  = document.getElementById('adminForm');
	var out   = document.querySelector('.mm-preview strong');
	var timer = null;
	function collect() {
		var fd = new FormData();
		var all = document.getElementById('field-all');
		if (all && all.checked) { fd.append('rcpt_all', '1'); }
		var g = document.getElementById('field-groups'); if (g) { fd.append('groups[]', g.value); }
		var u = document.getElementById('field-users');  if (u) { fd.append('users', u.value); }
		var e = document.getElementById('field-emails'); if (e) { fd.append('emails', e.value); }
		var ig = document.querySelector('input[name="ignore_optout"]');
		if (ig && ig.checked) { fd.append('ignore_optout', '1'); }
		return fd;
	}
	function update() {
		if (!form || !out) { return; }
		fetch(form.getAttribute('action') + '&task=preview', {
			method: 'POST', body: collect(), credentials: 'same-origin',
			headers: { 'X-Requested-With': 'XMLHttpRequest' }
		})
		.then(function (r) { return r.json(); })
		.then(function (d) { if (d && typeof d.text === 'string') { out.textContent = d.text; } })
		.catch(function () {});
	}
	function schedule() { clearTimeout(timer); timer = setTimeout(update, 300); }

	// "Everyone" subsumes groups/people; dim them (emails stay additive).
	var allChk  = document.getElementById('field-all');
	var sources = document.getElementById('mm-rcpt-sources');
	function reflectAll() { if (sources) { sources.classList.toggle('is-dim', !!(allChk && allChk.checked)); } }
	if (allChk) { allChk.addEventListener('change', function () { reflectAll(); schedule(); }); reflectAll(); }

	['field-groups', 'field-users', 'field-emails'].forEach(function (id) {
		var el = document.getElementById(id);
		if (el) { el.addEventListener('input', schedule); }
	});
	var ign = document.querySelector('input[name="ignore_optout"]');
	if (ign) { ign.addEventListener('change', schedule); }

	// --- Opt-out note: informational until the override is actually enabled ---
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
	// Open the picker when the calendar icon (not just the text) is clicked.
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

	// --- Autocomplete for the group / user pickers ---------------------------
	function attachAutocomplete(inputId, listId, task) {
		var gi = document.getElementById(inputId);
		var ac = document.getElementById(listId);
		if (!gi || !ac) { return; }
		var acTimer = null, acItems = [], acActive = -1;
		function curToken() { var v = gi.value; return v.slice(v.lastIndexOf(',') + 1).trim(); }
		function setToken(cn) {
			var v = gi.value, i = v.lastIndexOf(',');
			gi.value = (i >= 0 ? v.slice(0, i + 1) + ' ' : '') + cn + ', ';
		}
		function hideAc() { ac.hidden = true; ac.innerHTML = ''; acItems = []; acActive = -1; }
		function renderAc(list) {
			acItems = list; acActive = -1;
			if (!list || !list.length) { hideAc(); return; }
			ac.innerHTML = '';
			list.forEach(function (g) {
				var li = document.createElement('li');
				var cn = document.createElement('span'); cn.className = 'mm-ac-cn'; cn.textContent = g.cn;
				var ti = document.createElement('span'); ti.className = 'mm-ac-title'; ti.textContent = g.title ? (' — ' + g.title) : '';
				li.appendChild(cn); li.appendChild(ti);
				li.addEventListener('mousedown', function (e) { e.preventDefault(); setToken(g.cn); hideAc(); gi.focus(); schedule(); });
				ac.appendChild(li);
			});
			ac.hidden = false;
		}
		function lookup() {
			var tok = curToken();
			if (tok.length < 2) { hideAc(); return; }
			fetch(form.getAttribute('action') + '&task=' + task + '&q=' + encodeURIComponent(tok), {
				credentials: 'same-origin', headers: { 'X-Requested-With': 'XMLHttpRequest' }
			}).then(function (r) { return r.json(); }).then(renderAc).catch(hideAc);
		}
		gi.addEventListener('input', function () { clearTimeout(acTimer); acTimer = setTimeout(lookup, 200); });
		gi.addEventListener('keydown', function (e) {
			if (ac.hidden) { return; }
			var lis = ac.querySelectorAll('li');
			if (e.key === 'ArrowDown') { e.preventDefault(); acActive = Math.min(acActive + 1, lis.length - 1); }
			else if (e.key === 'ArrowUp') { e.preventDefault(); acActive = Math.max(acActive - 1, 0); }
			else if (e.key === 'Enter' && acActive >= 0) { e.preventDefault(); setToken(acItems[acActive].cn); hideAc(); schedule(); return; }
			else if (e.key === 'Escape') { hideAc(); return; }
			else { return; }
			for (var i = 0; i < lis.length; i++) { lis[i].classList.toggle('active', i === acActive); }
		});
		gi.addEventListener('blur', function () { setTimeout(hideAc, 150); });
	}
	attachAutocomplete('field-groups', 'mm-group-ac', 'grouplookup');
	attachAutocomplete('field-users', 'mm-user-ac', 'userlookup');

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
