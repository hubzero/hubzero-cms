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
$spec  = json_decode((string) $this->row->get('recipient_spec'), true) ?: array();
$type  = isset($spec['type']) ? $spec['type'] : 'all';
$spec['type'] = $type; // a new mailing has no saved type; preview the default selection, not 0
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
.mm-rcpt-types { margin-bottom:8px; }
.mm-rcpt-types label { display:flex; align-items:center; gap:6px; margin:5px 0; }
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

					<div class="input-wrap mm-rcpt-types">
						<label><input type="radio" name="recipient_type" value="all"<?php echo ($type == 'all' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_ALL'); ?></label>
						<label><input type="radio" name="recipient_type" value="group"<?php echo ($type == 'group' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_GROUP'); ?></label>
						<label><input type="radio" name="recipient_type" value="user"<?php echo ($type == 'user' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_USER'); ?></label>
						<label><input type="radio" name="recipient_type" value="email"<?php echo ($type == 'email' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_EMAIL'); ?></label>
					</div>

					<div class="input-wrap" data-rcpt="group">
						<label for="field-groups"><?php echo Lang::txt('COM_MASSMAIL_GROUP_IDS'); ?></label>
						<div class="mm-ac-wrap">
							<input type="text" name="groups[]" id="field-groups" value="<?php echo $this->escape(implode(', ', isset($spec['groups']) ? (array) $spec['groups'] : array())); ?>" placeholder="e.g. composites-team, 3042" autocomplete="off" />
							<ul class="mm-ac-list" id="mm-group-ac" hidden></ul>
						</div>
					</div>
					<div class="input-wrap" data-rcpt="user">
						<label for="field-users"><?php echo Lang::txt('COM_MASSMAIL_USER_IDS'); ?></label>
						<div class="mm-ac-wrap">
							<input type="text" name="users" id="field-users" value="<?php echo $this->escape(implode(', ', isset($spec['users']) ? (array) $spec['users'] : array())); ?>" placeholder="e.g. jdoe, 1002" autocomplete="off" />
							<ul class="mm-ac-list" id="mm-user-ac" hidden></ul>
						</div>
					</div>
					<div class="input-wrap" data-rcpt="email">
						<label for="field-emails"><?php echo Lang::txt('COM_MASSMAIL_RAW_EMAILS'); ?></label>
						<textarea name="emails" id="field-emails" rows="5" placeholder="one per line"><?php echo $this->escape(implode("\n", isset($spec['emails']) ? (array) $spec['emails'] : array())); ?></textarea>
					</div>
				</div>

				<div class="mm-card">
					<h4><?php echo Lang::txt('COM_MASSMAIL_DELIVERY'); ?></h4>
					<div class="input-wrap">
						<label for="field-schedule"><?php echo Lang::txt('COM_MASSMAIL_SCHEDULE'); ?></label>
						<?php echo Html::input('calendar', 'scheduled_at', $scheduledLocal, array('id' => 'field-schedule', 'format' => 'Y-m-d H:i:s')); ?>
						<p class="hint"><?php echo Lang::txt('COM_MASSMAIL_SCHEDULE_HINT'); ?></p>
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
	// --- Recipient picker: show the relevant input for the chosen type --------
	function sync() {
		var t = document.querySelector('input[name="recipient_type"]:checked');
		var val = t ? t.value : 'all';
		var blocks = document.querySelectorAll('[data-rcpt]');
		for (var i = 0; i < blocks.length; i++) {
			blocks[i].style.display = (blocks[i].getAttribute('data-rcpt') === val) ? '' : 'none';
		}
	}
	var radios = document.querySelectorAll('input[name="recipient_type"]');
	for (var i = 0; i < radios.length; i++) { radios[i].addEventListener('change', sync); }
	sync();

	// --- Live audience prediction --------------------------------------------
	var form  = document.getElementById('adminForm');
	var out   = document.querySelector('.mm-preview strong');
	var timer = null;
	function collect() {
		var fd = new FormData();
		var t = document.querySelector('input[name="recipient_type"]:checked');
		fd.append('recipient_type', t ? t.value : 'all');
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
	for (var j = 0; j < radios.length; j++) { radios[j].addEventListener('change', schedule); }
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
