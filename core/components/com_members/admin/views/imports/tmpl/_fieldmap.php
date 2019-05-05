<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->import->get('id')) { ?>
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_MAPPING'); ?></span></legend>

		<table class="field-map">
			<thead>
				<tr>
					<th><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_COL_FIELD_COLUMN'); ?></th>
					<th><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_COL_FIELD_MEMBER'); ?></th>
				</tr>
			</thead>
			<tbody>
			<?php foreach ($this->import->fields() as $mapping) { ?>
				<tr<?php if (!$mapping['field']) { echo ' class="field-unknown"'; } ?>>
					<td>
						<label for="mapping-<?php echo $mapping['name']; ?>">
							<?php echo $mapping['label']; ?>
						</label>
					</td>
					<td>
						<input type="hidden" name="mapping[<?php echo $mapping['name']; ?>][name]" value="<?php echo $this->escape($mapping['name']); ?>" />
						<input type="hidden" name="mapping[<?php echo $mapping['name']; ?>][label]" value="<?php echo $this->escape($mapping['label']); ?>" />
						<select name="mapping[<?php echo $mapping['name']; ?>][field]" id="mapping-<?php echo $mapping['name']; ?>">
							<option value=""><?php echo Lang::txt('COM_MEMBERS_UNKNOWN'); ?></option>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_ACCOUNT'); ?>">
								<option value="id" <?php if ($mapping['field'] == 'id') { echo 'selected="selected"'; } ?>>id</option>
								<option value="username" <?php if ($mapping['field'] == 'username') { echo 'selected="selected"'; } ?>>username</option>
								<option value="password" <?php if ($mapping['field'] == 'password') { echo 'selected="selected"'; } ?>>password</option>
								<option value="email" <?php if ($mapping['field'] == 'email') { echo 'selected="selected"'; } ?>>email</option>
								<option value="activation" <?php if ($mapping['field'] == 'activation') { echo 'selected="selected"'; } ?>>activation</option>
								<option value="sendEmail" <?php if ($mapping['field'] == 'sendEmail') { echo 'selected="selected"'; } ?>>sendEmail</option>
								<option value="usageAgreement" <?php if ($mapping['field'] == 'usageAgreement') { echo 'selected="selected"'; } ?>>usageAgreement</option>
								<option value="note" <?php if ($mapping['field'] == 'note') { echo 'selected="selected"'; } ?>>note</option>
								<option value="homeDirectory" <?php if ($mapping['field'] == 'homeDirectory') { echo 'selected="selected"'; } ?>>homeDirectory</option>
								<option value="modifiedDate" <?php if ($mapping['field'] == 'modifiedDate') { echo 'selected="selected"'; } ?>>modifiedDate</option>
								<option value="block" <?php if ($mapping['field'] == 'block') { echo 'selected="selected"'; } ?>>block</option>
								<option value="approved" <?php if ($mapping['field'] == 'approved') { echo 'selected="selected"'; } ?>>approved</option>
								<option value="loginShell" <?php if ($mapping['field'] == 'loginShell') { echo 'selected="selected"'; } ?>>loginShell</option>
								<option value="ftpShell" <?php if ($mapping['field'] == 'ftpShell') { echo 'selected="selected"'; } ?>>ftpShell</option>
								<option value="groups" <?php if ($mapping['field'] == 'groups') { echo 'selected="selected"'; } ?>>groups</option>
								<option value="projects" <?php if ($mapping['field'] == 'projects') { echo 'selected="selected"'; } ?>>projects</option>
								<option value="access" <?php if ($mapping['field'] == 'access') { echo 'selected="selected"'; } ?>>access</option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_REGISTER'); ?>">
								<option value="registerIP" <?php if ($mapping['field'] == 'registerIP') { echo 'selected="selected"'; } ?>>registerIP</option>
								<option value="registerHost" <?php if ($mapping['field'] == 'registerHost') { echo 'selected="selected"'; } ?>>registerHost</option>
								<option value="registerDate" <?php if ($mapping['field'] == 'registerDate') { echo 'selected="selected"'; } ?>>registerDate</option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_NAME'); ?>">
								<option value="name" <?php if ($mapping['field'] == 'name') { echo 'selected="selected"'; } ?>>name</option>
								<option value="givenName" <?php if ($mapping['field'] == 'givenName') { echo 'selected="selected"'; } ?>>givenName</option>
								<option value="middleName" <?php if ($mapping['field'] == 'middleName') { echo 'selected="selected"'; } ?>>middleName</option>
								<option value="surname" <?php if ($mapping['field'] == 'surname') { echo 'selected="selected"'; } ?>>surname</option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_PROFILE'); ?>">
								<?php
								include_once Component::path('com_members') . DS . 'models' . DS . 'profile' . DS . 'field.php';

								$fields = Components\Members\Models\Profile\Field::all()
									->ordered()
									->rows();

								foreach ($fields as $field)
								{
									?>
									<option value="<?php echo $field->get('name'); ?>" <?php if ($mapping['field'] == $field->get('name')) { echo 'selected="selected"'; } ?>><?php echo $field->get('name'); ?></option>
									<?php
								}
								?>
							</optgroup>
						</select>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</fieldset>
<?php }
