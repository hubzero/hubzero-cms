<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Members\Helpers\Admin::getActions('component');

Toolbar::title(Lang::txt('COM_MEMBERS_REGISTRATION'), 'users');
if ($canDo->get('core.edit')) {
    Toolbar::preferences($this->option);
    Toolbar::save();
    Toolbar::cancel();
}

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
    $this->view('_submenu')
         ->display();
?>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="adminForm"
    class="editform form-validate"
    data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
    <fieldset>
        <table class="adminlist">
            <thead>
                <tr>
                    <th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_AREA'); ?></th>
                    <th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_CREATE_ACCOUNT'); ?></th>
                    <th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_PROXY_CREATE_ACCOUNT'); ?></th>
                    <th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_UPDATE_ACCOUNT'); ?></th>
                    <th scope="col"><?php echo Lang::txt('COM_MEMBERS_COL_EDIT_ACCOUNT'); ?></th>
                </tr>
            </thead>
            <tbody>
<?php
foreach ($this->params as $field => $values) {
    if (substr($field, 0, strlen('registration')) == 'registration') {
        $title = $values->title;
        $value = $values->value;

        $create = strtoupper(substr($value, 0, 1));
        $proxy  = strtoupper(substr($value, 1, 1));
        $update = strtoupper(substr($value, 2, 1));
        $edit   = strtoupper(substr($value, 3, 1));

        $field = str_replace('registration', '', $values->name);
        ?>
                <tr>
                    <td><?php echo $title; ?></td>
                    <td>
                        <?php if ($create != '-') : ?>
                            <select name="settings[<?php echo $field; ?>][create]">
                                <?php $sel = ($create == 'O') ? ' selected="selected"' : ''; ?>
                                <option value="O"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL');
                                ?></option>
                                <?php $sel = ($create == 'R') ? ' selected="selected"' : ''; ?>
                                <option value="R"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED');
                                ?></option>
                                <?php $sel = ($create == 'H') ? ' selected="selected"' : ''; ?>
                                <option value="H"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE');
                                ?></option>
                                <?php $sel = ($create == 'U') ? ' selected="selected"' : ''; ?>
                                <option value="U"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY');
                                ?></option>
                            </select>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
                            <input type="hidden" name="settings[<?php echo $field; ?>][create]" value="-">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($proxy != '-') : ?>
                            <select name="settings[<?php echo $field; ?>][proxy]">
                                <?php $sel = ($proxy == 'O') ? ' selected="selected"' : ''; ?>
                                <option value="O"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL');
                                ?></option>
                                <?php $sel = ($proxy == 'R') ? ' selected="selected"' : ''; ?>
                                <option value="R"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED');
                                ?></option>
                                <?php $sel = ($proxy == 'H') ? ' selected="selected"' : ''; ?>
                                <option value="H"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE');
                                ?></option>
                                <?php $sel = ($proxy == 'U') ? ' selected="selected"' : ''; ?>
                                <option value="U"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY');
                                ?></option>
                            </select>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
                            <input type="hidden" name="settings[<?php echo $field; ?>][proxy]" value="-">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($update != '-') : ?>
                            <select name="settings[<?php echo $field; ?>][update]">
                                <?php $sel = ($update == 'O') ? ' selected="selected"' : ''; ?>
                                <option value="O"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL');
                                ?></option>
                                <?php $sel = ($update == 'R') ? ' selected="selected"' : ''; ?>
                                <option value="R"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED');
                                ?></option>
                                <?php $sel = ($update == 'H') ? ' selected="selected"' : ''; ?>
                                <option value="H"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE');
                                ?></option>
                                <?php $sel = ($update == 'U') ? ' selected="selected"' : ''; ?>
                                <option value="U"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY');
                                ?></option>
                            </select>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
                            <input type="hidden" name="settings[<?php echo $field; ?>][update]" value="-">
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($edit != '-') : ?>
                            <select name="settings[<?php echo $field; ?>][edit]">
                                <?php $sel = ($edit == 'O') ? ' selected="selected"' : ''; ?>
                                <option value="O"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_OPTIONAL');
                                ?></option>
                                <?php $sel = ($edit == 'R') ? ' selected="selected"' : ''; ?>
                                <option value="R"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_REQUIRED');
                                ?></option>
                                <?php $sel = ($edit == 'H') ? ' selected="selected"' : ''; ?>
                                <option value="H"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_HIDE');
                                ?></option>
                                <?php $sel = ($edit == 'U') ? ' selected="selected"' : ''; ?>
                                <option value="U"<?php echo $sel; ?>><?php
                                    echo Lang::txt('COM_MEMBERS_REGISTRATION_READ_ONLY');
                                ?></option>
                            </select>
                        <?php else : ?>
                            <?php echo Lang::txt('COM_MEMBERS_NOT_APPLICABLE'); ?>
                            <input type="hidden" name="settings[<?php echo $field; ?>][edit]" value="-">
                        <?php endif; ?>
                    </td>
                </tr>
        <?php
    }
}
?>
            </tbody>
        </table>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="save" />

        <?php echo Html::input('token'); ?>
    </fieldset>
</form>