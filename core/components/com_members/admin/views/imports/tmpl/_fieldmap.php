<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Lang;

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
                <tr<?php echo !$mapping['field'] ? ' class="field-unknown"' : ''; ?>>
                    <td>
                        <label for="mapping-<?php echo $mapping['name']; ?>">
                            <?php echo $mapping['label']; ?>
                        </label>
                    </td>
                    <td>
                        <input
                            type="hidden"
                            name="mapping[<?php echo $mapping['name']; ?>][name]"
                            value="<?php echo $this->escape($mapping['name']); ?>"/>
                        <input
                            type="hidden"
                            name="mapping[<?php echo $mapping['name']; ?>][label]"
                            value="<?php echo $this->escape($mapping['label']); ?>"/>
                        <select
                            name="mapping[<?php echo $mapping['name']; ?>][field]"
                            id="mapping-<?php echo $mapping['name']; ?>">
                            <option value=""><?php echo Lang::txt('COM_MEMBERS_UNKNOWN'); ?></option>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_ACCOUNT'); ?>">
                                <option
                                    value="id" <?= $mapping['field'] == 'id' ? 'selected="selected"' : '' ?>>id</option>
                                <option value="username" <?= $mapping['field'] == 'username' ? 'selected="selected"' : '' ?>>username</option>
                                <option value="password" <?= $mapping['field'] == 'password' ? 'selected="selected"' : '' ?>>password</option>
                                <option value="email" <?= $mapping['field'] == 'email' ? 'selected="selected"' : '' ?>>email</option>
                                <option value="activation" <?= $mapping['field'] == 'activation' ? 'selected="selected"' : '' ?>>activation</option>
                                <option value="sendEmail" <?= $mapping['field'] == 'sendEmail' ? 'selected="selected"' : '' ?>>sendEmail</option>
                                <option value="usageAgreement" <?= $mapping['field'] == 'usageAgreement' ? 'selected="selected"' : '' ?>>usageAgreement</option>
                                <option value="note" <?= $mapping['field'] == 'note' ? 'selected="selected"' : '' ?>>note</option>
                                <option value="homeDirectory" <?= $mapping['field'] == 'homeDirectory' ? 'selected="selected"' : '' ?>>homeDirectory</option>
                                <option value="modifiedDate" <?= $mapping['field'] == 'modifiedDate' ? 'selected="selected"' : '' ?>>modifiedDate</option>
                                <option value="block" <?= $mapping['field'] == 'block' ? 'selected="selected"' : '' ?>>block</option>
                                <option value="approved" <?= $mapping['field'] == 'approved' ? 'selected="selected"' : '' ?>>approved</option>
                                <option value="loginShell" <?= $mapping['field'] == 'loginShell' ? 'selected="selected"' : '' ?>>loginShell</option>
                                <option value="ftpShell" <?= $mapping['field'] == 'ftpShell' ? 'selected="selected"' : '' ?>>ftpShell</option>
                                <option value="groups" <?= $mapping['field'] == 'groups' ? 'selected="selected"' : '' ?>>groups</option>
                                <option value="projects" <?= $mapping['field'] == 'projects' ? 'selected="selected"' : '' ?>>projects</option>
                                <option value="access" <?= $mapping['field'] == 'access' ? 'selected="selected"' : '' ?>>access</option>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_REGISTER'); ?>">
                                <option value="registerIP" <?= $mapping['field'] == 'registerIP' ? 'selected="selected"' : '' ?>>registerIP</option>
                                <option value="registerHost" <?= $mapping['field'] == 'registerHost' ? 'selected="selected"' : '' ?>>registerHost</option>
                                <option value="registerDate" <?= $mapping['field'] == 'registerDate' ? 'selected="selected"' : '' ?>>registerDate</option>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_NAME'); ?>">
                                <option value="name" <?= $mapping['field'] == 'name' ? 'selected="selected"' : '' ?>>name</option>
                                <option value="givenName" <?= $mapping['field'] == 'givenName' ? 'selected="selected"' : '' ?>>givenName</option>
                                <option value="middleName" <?= $mapping['field'] == 'middleName' ? 'selected="selected"' : '' ?>>middleName</option>
                                <option value="surname" <?= $mapping['field'] == 'surname' ? 'selected="selected"' : '' ?>>surname</option>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_PROFILE'); ?>">
                                <?php
                                $fields = \Components\Members\Models\Profile\Field::all()
                                    ->ordered()
                                    ->rows();

                                foreach ($fields as $field) {
                                    ?>
                                    <?php $val = $field->get('name'); ?>
                                    <?php $sel = ($mapping['field'] == $val) ? 'selected="selected"' : ''; ?>
                                    <option value="<?php echo $val; ?>" <?php echo $sel; ?>><?php echo $val; ?></option>
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
