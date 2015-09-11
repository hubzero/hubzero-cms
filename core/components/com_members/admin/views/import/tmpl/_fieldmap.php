<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->import->exists()) { ?>
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
								<option value="uidNumber" <?php if ($mapping['field'] == 'uidNumber') { echo 'selected="selected"'; } ?>>uidNumber</option>
								<option value="username" <?php if ($mapping['field'] == 'username') { echo 'selected="selected"'; } ?>>username</option>
								<option value="password" <?php if ($mapping['field'] == 'password') { echo 'selected="selected"'; } ?>>password</option>
								<option value="email" <?php if ($mapping['field'] == 'email') { echo 'selected="selected"'; } ?>>email</option>
								<option value="emailConfirmed" <?php if ($mapping['field'] == 'emailConfirmed') { echo 'selected="selected"'; } ?>>emailConfirmed</option>
								<option value="mailPreferenceOption" <?php if ($mapping['field'] == 'mailPreferenceOption') { echo 'selected="selected"'; } ?>>mailPreferenceOption</option>
								<option value="usageAgreement" <?php if ($mapping['field'] == 'usageAgreement') { echo 'selected="selected"'; } ?>>usageAgreement</option>
								<option value="note" <?php if ($mapping['field'] == 'note') { echo 'selected="selected"'; } ?>>note</option>
								<option value="homeDirectory" <?php if ($mapping['field'] == 'homeDirectory') { echo 'selected="selected"'; } ?>>homeDirectory</option>
								<option value="modifiedDate" <?php if ($mapping['field'] == 'modifiedDate') { echo 'selected="selected"'; } ?>>modifiedDate</option>
								<option value="locked" <?php if ($mapping['field'] == 'locked') { echo 'selected="selected"'; } ?>>locked</option>
								<option value="groups" <?php if ($mapping['field'] == 'groups') { echo 'selected="selected"'; } ?>>groups</option>
							</optgroup>
							<optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_REGISTER'); ?>">
								<option value="regIP" <?php if ($mapping['field'] == 'regIP') { echo 'selected="selected"'; } ?>>regIP</option>
								<option value="regHost" <?php if ($mapping['field'] == 'regHost') { echo 'selected="selected"'; } ?>>regHost</option>
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
