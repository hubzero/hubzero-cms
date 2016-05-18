<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
								<option value="groups" <?php if ($mapping['field'] == 'groups') { echo 'selected="selected"'; } ?>>groups</option>
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
								<option value="bio" <?php if ($mapping['field'] == 'bio') { echo 'selected="selected"'; } ?>>bio</option>
								<option value="organization" <?php if ($mapping['field'] == 'organization') { echo 'selected="selected"'; } ?>>organization</option>
								<option value="orgtype" <?php if ($mapping['field'] == 'orgtype') { echo 'selected="selected"'; } ?>>orgtype</option>
								<option value="phone" <?php if ($mapping['field'] == 'phone') { echo 'selected="selected"'; } ?>>phone</option>
								<option value="public" <?php if ($mapping['field'] == 'public') { echo 'selected="selected"'; } ?>>public</option>
								<option value="url" <?php if ($mapping['field'] == 'url') { echo 'selected="selected"'; } ?>>url</option>
								<option value="orcid" <?php if ($mapping['field'] == 'orcid') { echo 'selected="selected"'; } ?>>orcid</option>
								<option value="interests" <?php if ($mapping['field'] == 'interests') { echo 'selected="selected"'; } ?>>interests</option>
								<option value="reason" <?php if ($mapping['field'] == 'reason') { echo 'selected="selected"'; } ?>>reason</option>
								<option value="picture" <?php if ($mapping['field'] == 'picture') { echo 'selected="selected"'; } ?>>picture</option>
								<option value="vip" <?php if ($mapping['field'] == 'vip') { echo 'selected="selected"'; } ?>>vip</option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_DEMOGRAPHICS'); ?>">
								<option value="countryresident" <?php if ($mapping['field'] == 'countryresident') { echo 'selected="selected"'; } ?>>countryresident</option>
								<option value="countryorigin" <?php if ($mapping['field'] == 'countryorigin') { echo 'selected="selected"'; } ?>>countryorigin</option>
								<option value="gender" <?php if ($mapping['field'] == 'gender') { echo 'selected="selected"'; } ?>>gender</option>
								<option value="disability" <?php if ($mapping['field'] == 'disability') { echo 'selected="selected"'; } ?>>disability</option>
								<option value="race" <?php if ($mapping['field'] == 'race') { echo 'selected="selected"'; } ?>>race</option>
								<option value="hispanic" <?php if ($mapping['field'] == 'hispanic') { echo 'selected="selected"'; } ?>>hispanic</option>
								<option value="nativeTribe" <?php if ($mapping['field'] == 'nativeTribe') { echo 'selected="selected"'; } ?>>nativeTribe</option>
							</optgroup>
						</select>
					</td>
				</tr>
			<?php } ?>
			</tbody>
		</table>
	</fieldset>
<?php }
