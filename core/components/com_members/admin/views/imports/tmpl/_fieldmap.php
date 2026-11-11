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
                                <?php
                                $selectedAttr = $mapping['field'] == 'username' ? 'selected="selected"' : '';
                                ?>
                                <option value="username" <?= $selectedAttr ?>>username</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'password' ? 'selected="selected"' : '';
                                ?>
                                <option value="password" <?= $selectedAttr ?>>password</option>
                                <option value="email"
                                    <?= $mapping['field'] == 'email' ? 'selected="selected"' : '' ?>>email</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'activation' ? 'selected="selected"' : '';
                                ?>
                                <option value="activation" <?= $selectedAttr ?>>activation</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'sendEmail' ? 'selected="selected"' : '';
                                ?>
                                <option value="sendEmail" <?= $selectedAttr ?>>sendEmail</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'usageAgreement' ? 'selected="selected"' : '';
                                ?>
                                <option value="usageAgreement" <?= $selectedAttr ?>>usageAgreement</option>
                                <option value="note"
                                    <?= $mapping['field'] == 'note' ? 'selected="selected"' : '' ?>>note</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'homeDirectory' ? 'selected="selected"' : '';
                                ?>
                                <option value="homeDirectory" <?= $selectedAttr ?>>homeDirectory</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'modifiedDate' ? 'selected="selected"' : '';
                                ?>
                                <option value="modifiedDate" <?= $selectedAttr ?>>modifiedDate</option>
                                <option value="block"
                                    <?= $mapping['field'] == 'block' ? 'selected="selected"' : '' ?>>block</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'approved' ? 'selected="selected"' : '';
                                ?>
                                <option value="approved" <?= $selectedAttr ?>>approved</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'loginShell' ? 'selected="selected"' : '';
                                ?>
                                <option value="loginShell" <?= $selectedAttr ?>>loginShell</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'ftpShell' ? 'selected="selected"' : '';
                                ?>
                                <option value="ftpShell" <?= $selectedAttr ?>>ftpShell</option>
                                <option value="groups"
                                    <?= $mapping['field'] == 'groups' ? 'selected="selected"' : '' ?>>groups</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'projects' ? 'selected="selected"' : '';
                                ?>
                                <option value="projects" <?= $selectedAttr ?>>projects</option>
                                <option value="access"
                                    <?= $mapping['field'] == 'access' ? 'selected="selected"' : '' ?>>access</option>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_REGISTER'); ?>">
                                <?php
                                $selectedAttr = $mapping['field'] == 'registerIP' ? 'selected="selected"' : '';
                                ?>
                                <option value="registerIP" <?= $selectedAttr ?>>registerIP</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'registerHost' ? 'selected="selected"' : '';
                                ?>
                                <option value="registerHost" <?= $selectedAttr ?>>registerHost</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'registerDate' ? 'selected="selected"' : '';
                                ?>
                                <option value="registerDate" <?= $selectedAttr ?>>registerDate</option>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_MEMBERS_IMPORT_FIELDS_NAME'); ?>">
                                <option value="name"
                                    <?= $mapping['field'] == 'name' ? 'selected="selected"' : '' ?>>name</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'givenName' ? 'selected="selected"' : '';
                                ?>
                                <option value="givenName" <?= $selectedAttr ?>>givenName</option>
                                <?php
                                $selectedAttr = $mapping['field'] == 'middleName' ? 'selected="selected"' : '';
                                ?>
                                <option value="middleName" <?= $selectedAttr ?>>middleName</option>
                                <option value="surname"
                                    <?= $mapping['field'] == 'surname' ? 'selected="selected"' : '' ?>>surname</option>
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
