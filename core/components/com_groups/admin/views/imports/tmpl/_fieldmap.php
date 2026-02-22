<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

if ($this->import->get('id')) { ?>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_FIELDSET_MAPPING'); ?></span></legend>

        <table class="field-map">
            <thead>
                <tr>
                    <th><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_COLUMN'); ?></th>
                    <th><?php echo Lang::txt('COM_GROUPS_IMPORT_EDIT_COL_FIELD_MEMBER'); ?></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($this->import->fields() as $mapping) { ?>
                <tr<?php if (!$mapping['field']) {
                    echo ' class="field-unknown"';
                   } ?>>
                    <td>
                        <label for="mapping-<?php echo $mapping['name']; ?>">
                            <?php echo $mapping['label']; ?>
                        </label>
                    </td>
                    <td>
                        <?php $mName = $mapping['name']; ?>
                        <input type="hidden"
                            name="mapping[<?php echo $mName; ?>][name]"
                            value="<?php echo $this->escape($mName); ?>"
                        />
                        <input type="hidden"
                            name="mapping[<?php echo $mName; ?>][label]"
                            value="<?php echo $this->escape($mapping['label']); ?>"
                        />
                        <select name="mapping[<?php echo $mName; ?>][field]"
                            id="mapping-<?php echo $mName; ?>"
                        >
                            <option value=""><?php echo Lang::txt('COM_GROUPS_UNKNOWN'); ?></option>
                            <?php
                            $columns = array(
                                'gidNumber',
                                'cn',
                                'description',
                                'published',
                                'approved',
                                //'public_desc',
                                //'private_desc',
                                'restrict_msg',
                                'join_policy',
                                'discoverability',
                                'discussion_email_autosubscribe',
                                'plugins',
                                'created',
                                'created_by',
                                'members',
                                'managers',
                                //'projects',
                                'tags'
                            );
                            ?>
                            <optgroup label="<?php echo Lang::txt('COM_GROUPS_IMPORT_FIELDS_DETAILS'); ?>">
                                <?php foreach ($columns as $column) : ?>
                                    <option value="<?php echo $column; ?>" <?php if ($mapping['field'] == $column) {
                                        echo 'selected="selected"';
                                                   } ?>><?php echo $column; ?></option>
                                <?php endforeach; ?>
                            </optgroup>
                            <optgroup label="<?php echo Lang::txt('COM_GROUPS_IMPORT_FIELDS_DESCRIPTION'); ?>">
                                <?php
                                $path = Component::path('com_groups')
                                    . DS . 'models' . DS . 'orm' . DS . 'field.php';
                                include_once $path;

                                $fields = \Components\Groups\Models\Orm\Field::all()
                                    ->ordered()
                                    ->rows();

                                foreach ($fields as $field) {
                                    ?>
                                    <?php $fn = $this->escape($field->get('name')); ?>
                                    <?php $sel = ($mapping['field'] == $field->get('name'))
                                        ? ' selected="selected"' : ''; ?>
                                    <option value="<?php echo $fn; ?>"<?php echo $sel; ?>><?php echo $fn; ?></option>
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