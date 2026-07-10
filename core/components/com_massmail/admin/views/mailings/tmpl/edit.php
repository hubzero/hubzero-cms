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
.mm-sending-off { margin:0 0 14px; padding:10px 16px; border-radius:6px; background:#fffaf0; border:1px solid #f6ad55; color:#9c4221; font-weight:600; }

/* Sender / reply-to header, email-client style. */
.mm-headers { margin:0 0 16px; max-width:680px; padding:12px 14px; background:#f7f8fa; border:1px solid #e2e8f0; border-radius:6px; }
.mm-header-fields { display:grid; grid-template-columns:110px 1fr; gap:8px 12px; align-items:center; }
.mm-header-fields label { color:#4a5568; font-size:13px; margin:0; }
.mm-header-fields input { width:100%; }
.mm-header-readonly { display:grid; grid-template-columns:110px 1fr; gap:6px 12px; margin:0; }
.mm-header-readonly dt { color:#718096; font-size:13px; }
.mm-header-readonly dd { margin:0; color:#1a202c; font-size:13px; font-weight:600; }
.mm-header-hint { margin:10px 0 0; color:#a0aec0; font-size:12px; }
/* Vertical recipient-type radios */
.mm-rcpt-types label { display:flex; align-items:center; gap:6px; margin:5px 0; }

/* Framed plain-text editor. */
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

<?php if (!$sendingOn) { ?>
<p class="mm-sending-off"><?php echo Lang::txt('COM_MASSMAIL_SENDING_OFF_WARNING'); ?></p>
<?php } ?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="hz-form">

	<div class="mm-headers">
		<?php if ($isSuper) { ?>
		<div class="mm-header-fields">
			<label for="field-from-name"><?php echo Lang::txt('COM_MASSMAIL_FROM_NAME'); ?></label>
			<input type="text" name="fields[from_name]" id="field-from-name" value="<?php echo $this->escape($this->row->get('from_name')); ?>" />
			<label for="field-from-email"><?php echo Lang::txt('COM_MASSMAIL_FROM_EMAIL'); ?></label>
			<input type="text" name="fields[from_email]" id="field-from-email" value="<?php echo $this->escape($this->row->get('from_email')); ?>" />
			<label for="field-reply-to"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></label>
			<input type="text" name="fields[reply_to]" id="field-reply-to" value="<?php echo $this->escape($this->row->get('reply_to')); ?>" />
		</div>
		<?php
		} else {
			$fromName  = trim((string) $this->row->get('from_name'));
			$fromEmail = trim((string) $this->row->get('from_email'));
			$replyTo   = trim((string) $this->row->get('reply_to'));
			$fromShown = $fromEmail ? ($fromName ? ($fromName . ' <' . $fromEmail . '>') : $fromEmail) : $fromName;
		?>
		<dl class="mm-header-readonly">
			<dt><?php echo Lang::txt('COM_MASSMAIL_FROM_NAME'); ?></dt>
			<dd><?php echo $this->escape($fromShown); ?></dd>
			<?php if ($replyTo) { ?>
			<dt><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></dt>
			<dd><?php echo $this->escape($replyTo); ?></dd>
			<?php } ?>
		</dl>
		<p class="mm-header-hint"><?php echo Lang::txt('COM_MASSMAIL_SENDER_LOCKED_HINT'); ?></p>
		<?php } ?>
	</div>

	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_MESSAGE'); ?></legend>

				<div class="input-wrap">
					<label for="field-subject"><?php echo Lang::txt('COM_MASSMAIL_SUBJECT'); ?> <span class="required">*</span></label>
					<input type="text" name="fields[subject]" id="field-subject" size="60" value="<?php echo $this->escape($this->row->get('subject')); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-body"><?php echo Lang::txt('COM_MASSMAIL_BODY'); ?> <span class="required">*</span></label>
					<p class="hint"><?php echo Lang::txt('COM_MASSMAIL_BODY_HINT'); ?></p>
					<?php
					$editor = \Hubzero\Html\Editor::getInstance('ckeditor5');
					echo $editor->display(
						'fields[body]',
						$this->row->get('body'),
						'100%', '350', 35, 12, false, 'field-body', null, null,
						array(
							// Keep it plain text, but give the editor a real (minimal)
							// toolbar so it reads as an editor rather than a bare box.
							// The attach paperclip is injected into this toolbar by JS.
							'toolbar' => array('undo', 'redo'),
							'removePlugins' => array('Heading', 'Bold', 'Italic', 'Strikethrough', 'Code', 'Link', 'List', 'BlockQuote', 'BulletedList', 'NumberedList')
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
			</fieldset>
		</div>

		<div class="col span4">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_RECIPIENTS'); ?></legend>

				<p class="mm-preview"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></p>

				<div class="input-wrap mm-rcpt-types">
					<label><input type="radio" name="recipient_type" value="all"<?php echo ($type == 'all' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_ALL'); ?></label>
					<label><input type="radio" name="recipient_type" value="group"<?php echo ($type == 'group' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_GROUP'); ?></label>
					<label><input type="radio" name="recipient_type" value="user"<?php echo ($type == 'user' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_USER'); ?></label>
					<label><input type="radio" name="recipient_type" value="email"<?php echo ($type == 'email' ? ' checked="checked"' : ''); ?> /> <?php echo Lang::txt('COM_MASSMAIL_RCPT_EMAIL'); ?></label>
				</div>

				<div class="input-wrap" data-rcpt="group">
					<label for="field-groups"><?php echo Lang::txt('COM_MASSMAIL_GROUP_IDS'); ?></label>
					<input type="text" name="groups[]" id="field-groups" value="<?php echo $this->escape(implode(',', isset($spec['groups']) ? (array) $spec['groups'] : array())); ?>" placeholder="e.g. 3041, 3042" />
				</div>
				<div class="input-wrap" data-rcpt="user">
					<label for="field-users"><?php echo Lang::txt('COM_MASSMAIL_USER_IDS'); ?></label>
					<input type="text" name="users" id="field-users" value="<?php echo $this->escape(implode(',', isset($spec['users']) ? (array) $spec['users'] : array())); ?>" placeholder="e.g. 1002, 1230" />
				</div>
				<div class="input-wrap" data-rcpt="email">
					<label for="field-emails"><?php echo Lang::txt('COM_MASSMAIL_RAW_EMAILS'); ?></label>
					<textarea name="emails" id="field-emails" rows="5" placeholder="one per line"><?php echo $this->escape(implode("\n", isset($spec['emails']) ? (array) $spec['emails'] : array())); ?></textarea>
				</div>
			</fieldset>

			<?php if ($canDo->get('core.override')) { ?>
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_COMPLIANCE'); ?></legend>
				<div class="input-wrap">
					<label class="mm-warn">
						<input type="checkbox" name="ignore_optout" value="1"<?php echo ($ignore ? ' checked="checked"' : ''); ?> />
						<?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT'); ?>
					</label>
					<p class="hint warning"><?php echo Lang::txt('COM_MASSMAIL_IGNORE_OPTOUT_HINT'); ?></p>
				</div>
			</fieldset>
			<?php } ?>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<?php echo Html::input('token'); ?>
</form>

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

		// Wait for CKEditor to build its toolbar, then drop a paperclip button in.
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
