<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2026 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->row->isNew() ? Lang::txt('JACTION_CREATE') : Lang::txt('JACTION_EDIT'));

Toolbar::title(Lang::txt('COM_SAML') . ': ' . Lang::txt('COM_SAML_SERVICE_PROVIDER') . ': ' . $text, 'saml');
if (User::authorise('core.edit', $this->option) || User::authorise('core.create', $this->option))
{
	Toolbar::apply();
	Toolbar::save();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$cert = $this->row->certificateInfo();

$allowedGroups = $this->row->allowedGroups();
$allowedGroups = $allowedGroups ? implode(', ', $allowedGroups) : '';

$nameidFormats = array(
	'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified'  => Lang::txt('COM_SAML_NAMEID_FORMAT_UNSPECIFIED'),
	'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress' => Lang::txt('COM_SAML_NAMEID_FORMAT_EMAIL'),
	'urn:oasis:names:tc:SAML:2.0:nameid-format:persistent'   => Lang::txt('COM_SAML_NAMEID_FORMAT_PERSISTENT')
);

$nameidSources = array(
	'username' => Lang::txt('COM_SAML_NAMEID_SOURCE_USERNAME'),
	'email'    => Lang::txt('COM_SAML_NAMEID_SOURCE_EMAIL'),
	'id'       => Lang::txt('COM_SAML_NAMEID_SOURCE_ID')
);
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')); ?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_FIELDSET_DETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_SAML_FIELD_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[name]" id="field-name" class="required" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('name', ''))); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_ENTITY_ID_HINT')); ?>">
					<label for="field-entity_id"><?php echo Lang::txt('COM_SAML_FIELD_ENTITY_ID'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[entity_id]" id="field-entity_id" class="required" maxlength="255" value="<?php echo $this->escape(stripslashes($this->row->get('entity_id', ''))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_ENTITY_ID_HINT'); ?></span>
				</div>

				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_SAML_FIELD_DESCRIPTION'); ?>:</label><br />
					<textarea name="fields[description]" id="field-description" cols="50" rows="3"><?php echo $this->escape(stripslashes($this->row->get('description', ''))); ?></textarea>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_ACS_HINT')); ?>">
					<label for="field-acs_url"><?php echo Lang::txt('COM_SAML_FIELD_ACS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[acs_url]" id="field-acs_url" class="required" maxlength="500" value="<?php echo $this->escape(stripslashes($this->row->get('acs_url', ''))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_ACS_HINT'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_SLO_HINT')); ?>">
					<label for="field-slo_url"><?php echo Lang::txt('COM_SAML_FIELD_SLO'); ?>:</label><br />
					<input type="text" name="fields[slo_url]" id="field-slo_url" maxlength="500" value="<?php echo $this->escape(stripslashes($this->row->get('slo_url', ''))); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_SLO_HINT'); ?></span>
				</div>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_FIELDSET_TRUST'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-want_requests_signed"><?php echo Lang::txt('COM_SAML_FIELD_WANT_SIGNED'); ?>:</label><br />
					<select name="fields[want_requests_signed]" id="field-want_requests_signed">
						<option value="1"<?php echo $this->row->get('want_requests_signed', 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JYES'); ?></option>
						<option value="0"<?php echo !$this->row->get('want_requests_signed', 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JNO'); ?></option>
					</select>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_SIGN_ASSERTION_HINT')); ?>">
					<label for="field-sign_assertion"><?php echo Lang::txt('COM_SAML_FIELD_SIGN_ASSERTION'); ?>:</label><br />
					<select name="fields[sign_assertion]" id="field-sign_assertion">
						<option value="0"<?php echo !$this->row->get('sign_assertion') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JNO'); ?></option>
						<option value="1"<?php echo $this->row->get('sign_assertion') ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JYES'); ?></option>
					</select>
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_SIGN_ASSERTION_HINT'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_CERT_HINT')); ?>">
					<label for="field-signing_cert"><?php echo Lang::txt('COM_SAML_FIELD_CERT'); ?>:</label><br />
					<textarea name="fields[signing_cert]" id="field-signing_cert" cols="50" rows="8" class="monospace"><?php echo $this->escape($this->row->get('signing_cert', '')); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_CERT_HINT'); ?></span>
				</div>

				<?php if ($cert) { ?>
					<table class="meta">
						<tbody>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_SAML_CERT_SUBJECT'); ?>:</th>
								<td><?php echo $this->escape($cert['subject']); ?></td>
							</tr>
							<tr>
								<th class="key"><?php echo Lang::txt('COM_SAML_CERT_EXPIRES'); ?>:</th>
								<td>
									<?php echo Date::of($cert['valid_to'])->toLocal(); ?>
									<?php if ($cert['valid_to'] < time()) { ?>
										<span class="error"><?php echo Lang::txt('COM_SAML_CERT_EXPIRED'); ?></span>
									<?php } ?>
								</td>
							</tr>
							<?php if ($cert['fingerprint']) { ?>
								<tr>
									<th class="key"><?php echo Lang::txt('COM_SAML_CERT_FINGERPRINT'); ?>:</th>
									<td><code><?php echo $this->escape($cert['fingerprint']); ?></code></td>
								</tr>
							<?php } ?>
						</tbody>
					</table>
				<?php } ?>
			</fieldset>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_FIELDSET_POLICY'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-nameid_source"><?php echo Lang::txt('COM_SAML_FIELD_NAMEID_SOURCE'); ?>:</label><br />
					<select name="fields[nameid_source]" id="field-nameid_source">
						<?php foreach ($nameidSources as $value => $label) { ?>
							<option value="<?php echo $this->escape($value); ?>"<?php echo $this->row->get('nameid_source', 'username') == $value ? ' selected="selected"' : ''; ?>><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</div>

				<div class="input-wrap">
					<label for="field-nameid_format"><?php echo Lang::txt('COM_SAML_FIELD_NAMEID_FORMAT'); ?>:</label><br />
					<select name="fields[nameid_format]" id="field-nameid_format">
						<?php foreach ($nameidFormats as $value => $label) { ?>
							<option value="<?php echo $this->escape($value); ?>"<?php echo $this->row->get('nameid_format', 'urn:oasis:names:tc:SAML:1.1:nameid-format:unspecified') == $value ? ' selected="selected"' : ''; ?>><?php echo $label; ?></option>
						<?php } ?>
					</select>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_ATTRIBUTE_MAP_HINT')); ?>">
					<label for="field-attribute_map"><?php echo Lang::txt('COM_SAML_FIELD_ATTRIBUTE_MAP'); ?>:</label><br />
					<textarea name="fields[attribute_map]" id="field-attribute_map" cols="50" rows="6" class="monospace"><?php echo $this->escape($this->row->get('attribute_map', '')); ?></textarea>
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_ATTRIBUTE_MAP_HINT'); ?></span>
				</div>

				<div class="input-wrap" data-hint="<?php echo $this->escape(Lang::txt('COM_SAML_FIELD_ALLOWED_GROUPS_HINT')); ?>">
					<label for="field-allowed_groups"><?php echo Lang::txt('COM_SAML_FIELD_ALLOWED_GROUPS'); ?>:</label><br />
					<input type="text" name="allowed_groups" id="field-allowed_groups" value="<?php echo $this->escape($allowedGroups); ?>" />
					<span class="hint"><?php echo Lang::txt('COM_SAML_FIELD_ALLOWED_GROUPS_HINT'); ?></span>
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<table class="meta">
				<tbody>
					<tr>
						<th class="key"><?php echo Lang::txt('COM_SAML_FIELD_ID'); ?>:</th>
						<td>
							<?php echo $this->row->get('id', 0); ?>
							<input type="hidden" name="fields[id]" id="field-id" value="<?php echo $this->row->get('id'); ?>" />
						</td>
					</tr>
					<?php if ($this->row->get('created')) { ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_FIELD_CREATED'); ?>:</th>
							<td><time datetime="<?php echo $this->row->get('created'); ?>"><?php echo $this->row->get('created'); ?></time></td>
						</tr>
					<?php } ?>
					<?php if ($this->row->get('metadata_url')) { ?>
						<tr>
							<th class="key"><?php echo Lang::txt('COM_SAML_FIELD_METADATA_URL'); ?>:</th>
							<td><code><?php echo $this->escape($this->row->get('metadata_url')); ?></code></td>
						</tr>
					<?php } ?>
				</tbody>
			</table>

			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_SAML_FIELDSET_PUBLISHING'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-state"><?php echo Lang::txt('JSTATUS'); ?>:</label><br />
					<?php if (User::authorise('core.edit.state', $this->option)) { ?>
						<select name="fields[state]" id="field-state">
							<option value="1"<?php echo $this->row->get('state', 1) == 1 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_STATE_ENABLED'); ?></option>
							<option value="0"<?php echo $this->row->get('state', 1) == 0 ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('COM_SAML_STATE_DISABLED'); ?></option>
						</select>
					<?php } else { ?>
						<span class="state <?php echo $this->row->get('state', 1) ? 'publish' : 'unpublish'; ?>">
							<span><?php echo $this->row->get('state', 1) ? Lang::txt('COM_SAML_STATE_ENABLED') : Lang::txt('COM_SAML_STATE_DISABLED'); ?></span>
						</span>
					<?php } ?>
				</div>

				<input type="hidden" name="fields[metadata_url]" value="<?php echo $this->escape($this->row->get('metadata_url', '')); ?>" />
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="task" value="save" autocomplete="off" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />

	<?php echo Html::input('token'); ?>
</form>
