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
<?php if (!$sendingOn) { ?>
<p class="mm-sending-off"><?php echo Lang::txt('COM_MASSMAIL_SENDING_OFF_WARNING'); ?></p>
<style>.mm-sending-off{margin:0 0 14px;padding:10px 16px;border-radius:6px;background:#fffaf0;border:1px solid #f6ad55;color:#9c4221;font-weight:600;}</style>
<?php } ?>
<style>
/* Give the plain-text editor a tidy framed look that matches the form inputs. */
#adminForm .ck.ck-toolbar { border-radius: 5px 5px 0 0; border-color: #ccc; background: #f7f8fa; }
#adminForm .ck.ck-editor__editable { border-color: #ccc; border-radius: 0 0 5px 5px; }
#adminForm .ck.ck-editor__editable.ck-focused { border-color: #4299e1; box-shadow: none; }
</style>
<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" class="hz-form">
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
							'toolbar' => array('undo', 'redo'),
							'removePlugins' => array('Heading', 'Bold', 'Italic', 'Strikethrough', 'Code', 'Link', 'List', 'BlockQuote', 'BulletedList', 'NumberedList')
						)
					);
					?>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_ATTACHMENTS'); ?></legend>
				<?php $atts = $this->row->attachments()->rows(); ?>
				<?php if (count($atts)) { ?>
					<ul class="mm-attachments">
					<?php foreach ($atts as $att) { ?>
						<li>
							<?php echo $this->escape($att->get('filename')); ?>
							(<?php echo round($att->get('size') / 1024) . ' KB'; ?>)
							<?php if (!$isNew) { ?>
								<a class="mm-remove" href="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=removeattachment&attachment=' . $att->get('id') . '&' . Session::getFormToken() . '=1'); ?>"><?php echo Lang::txt('JACTION_DELETE'); ?></a>
							<?php } ?>
						</li>
					<?php } ?>
					</ul>
				<?php } ?>
				<div class="input-wrap">
					<label for="field-attachment"><?php echo Lang::txt('COM_MASSMAIL_ADD_ATTACHMENT'); ?></label>
					<input type="file" name="attachment[]" id="field-attachment" multiple="multiple" />
				</div>
			</fieldset>
		</div>

		<div class="col span4">
			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_RECIPIENTS'); ?></legend>

				<p class="mm-preview"><strong><?php echo Lang::txt('COM_MASSMAIL_WILL_REACH', number_format($preview)); ?></strong></p>

				<div class="input-wrap">
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

			<fieldset class="adminform">
				<legend><?php echo Lang::txt('COM_MASSMAIL_SENDER'); ?></legend>
				<div class="input-wrap">
					<label for="field-from-name"><?php echo Lang::txt('COM_MASSMAIL_FROM_NAME'); ?></label>
					<input type="text" name="fields[from_name]" id="field-from-name" value="<?php echo $this->escape($this->row->get('from_name')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-from-email"><?php echo Lang::txt('COM_MASSMAIL_FROM_EMAIL'); ?></label>
					<input type="text" name="fields[from_email]" id="field-from-email" value="<?php echo $this->escape($this->row->get('from_email')); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-reply-to"><?php echo Lang::txt('COM_MASSMAIL_REPLY_TO'); ?></label>
					<input type="text" name="fields[reply_to]" id="field-reply-to" value="<?php echo $this->escape($this->row->get('reply_to')); ?>" />
				</div>
			</fieldset>

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

	// Live audience prediction: recompute the "will reach N" count from the
	// current picker selection whenever it changes.
	var form    = document.getElementById('adminForm');
	var out     = document.querySelector('.mm-preview strong');
	var timer   = null;
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
})();
</script>
