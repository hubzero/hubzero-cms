<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

$this->css('import')
     ->js('import');

$canDo = \Components\Members\Helpers\Admin::getActions('component');

// set title
$title = ($this->import->get('id'))
    ? Lang::txt('COM_MEMBERS_IMPORT_TITLE_EDIT')
    : Lang::txt('COM_MEMBERS_IMPORT_TITLE_ADD');

Toolbar::title(Lang::txt('COM_MEMBERS') . ': ' . $title, 'import');
if ($canDo->get('core.admin')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php foreach ($this->getErrors() as $error) : ?>
    <p class="error"><?php echo $error; ?></p>
<?php endforeach; ?>

<?php $formAction = Route::url('index.php?option=com_members&controller=import&task=save'); ?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    enctype="multipart/form-data"
    class="editform form-validate"
    data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-name">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_NAME'); ?>
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label>
                    <input
                        type="text"
                        name="import[name]"
                        id="field-name"
                        class="required"
                        value="<?php echo $this->escape($this->import->get('name')); ?>"/>
                </div>

                <div class="input-wrap">
                    <?php $text = Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_NOTES'); ?>
                    <label for="field-notes"><?php echo $text; ?></label><br />
                    <textarea
                        name="import[notes]"
                        id="field-notes"
                        rows="5"><?php echo $this->escape($this->import->get('notes')); ?></textarea>
                </div>
            </fieldset>

            <?php
                // parse our hooks
                $hooks = json_decode($this->import->get('hooks') ?: '');
            if (!is_object($hooks)) {
                $hooks = new stdClass();
            }
                $hooks->postparse   = (isset($hooks->postparse)) ? $hooks->postparse : array();
                $hooks->postmap     = (isset($hooks->postmap)) ? $hooks->postmap : array();
                $hooks->postconvert = (isset($hooks->postconvert)) ? $hooks->postconvert : array();
            ?>
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_HOOKS'); ?></span></legend>

                <?php if ($this->hooks->count()) { ?>
                    <div
                        class="input-wrap"
                        data-hint="<?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
                        <label for="field-name">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTPARSEHOOK'); ?>
                        </label><br />
                        <select name="hooks[postparse][]" multiple="multiple">
                            <?php if (isset($hooks->postparse)) : ?>
                                <?php foreach ($hooks->postparse as $hook) : ?>
                                    <?php $importHook = $this->hooks->fetch('id', $hook); ?>
                                    <?php $val = $importHook->get('name'); ?>
                                    <option
                                        selected="selected"
                                        value="<?php echo $importHook->get('id'); ?>"><?php echo $val; ?></option>
                                <?php endforeach; ?>
                            <?php endif; ?>

                            <?php foreach ($this->hooks as $hook) : ?>
                                <?php if (
                                $hook->get('event') != 'postparse' || in_array(
                                    $hook->get('id'),
                                    $hooks->postparse
                                )
) {
                                    continue;
                                } ?>
                                <?php $val = $hook->get('name'); ?>
                                <option value="<?php echo $hook->get('id'); ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a class="hook-up" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
                        </a> |
                        <a class="hook-down" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
                        </a><br />
                        <span class="hint">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
                        </span>
                    </div>
                    <div
                        class="input-wrap"
                        data-hint="<?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
                        <label for="field-hookpostmap">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTMAPHOOK'); ?>
                        </label>
                        <select name="hooks[postmap][]" id="field-hookpostmap" multiple="multiple">
                            <?php foreach ($hooks->postmap as $hook) : ?>
                                <?php $importHook = $this->hooks->fetch('id', $hook); ?>
                                <?php $val = $importHook->get('name'); ?>
                                <option
                                    selected="selected"
                                    value="<?php echo $importHook->get('id'); ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>

                            <?php foreach ($this->hooks as $hook) : ?>
                                <?php if (
                                $hook->get('event') != 'postmap' || in_array(
                                    $hook->get('id'),
                                    $hooks->postmap
                                )
) {
                                    continue;
                                } ?>
                                <?php $val = $hook->get('name'); ?>
                                <option value="<?php echo $hook->get('id'); ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a class="hook-up" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
                        </a> |
                        <a class="hook-down" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
                        </a><br />
                        <span class="hint">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
                        </span>
                    </div>
                    <div
                        class="input-wrap"
                        data-hint="<?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>">
                        <label for="field-hookpostconvert">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_POSTCONVERTHOOK'); ?>
                        </label>
                        <select name="hooks[postconvert][]" id="field-hookpostconvert" multiple="multiple">
                            <?php foreach ($hooks->postconvert as $hook) : ?>
                                <?php $importHook = $this->hooks->fetch('id', $hook); ?>
                                <?php $val = $importHook->get('name'); ?>
                                <option
                                    selected="selected"
                                    value="<?php echo $importHook->get('id'); ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                            <?php foreach ($this->hooks as $hook) : ?>
                                <?php if (
                                $hook->get('event') != 'postconvert' || in_array(
                                    $hook->get('id'),
                                    $hooks->postconvert
                                )
) {
                                    continue;
                                } ?>
                                <?php $val = $hook->get('name'); ?>
                                <option value="<?php echo $hook->get('id'); ?>"><?php echo $val; ?></option>
                            <?php endforeach; ?>
                        </select>
                        <a class="hook-up" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_UP'); ?>
                        </a> |
                        <a class="hook-down" href="#">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_DOWN'); ?>
                        </a><br />
                        <span class="hint">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_HOOKS_HINT'); ?>
                        </span>
                    </div>
                <?php } else { ?>
                    <div class="input-wrap">
                        <em><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_NO_HOOKS_FOUND'); ?></em>
                        <input type="hidden" name="hooks[postparse][]" value="" />
                        <input type="hidden" name="hooks[postmap][]" value="" />
                        <input type="hidden" name="hooks[postconvert][]" value="" />
                    </div>
                <?php } ?>
            </fieldset>
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_PARAMS'); ?></span></legend>

                <div
                    class="input-wrap"
                    data-hint="<?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED_HINT'); ?>">
                    <label for="param-approved">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED'); ?>
                    </label>
                    <select name="params[approved]" id="param-approved">
                        <?php $val = ($this->params->get('approved', 1) == 0) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $val; ?>><?php echo Lang::txt('JNO'); ?></option>
                        <?php $val = ($this->params->get('approved', 1) == 1) ? ' selected="selected"' : ''; ?>
                        <option value="1"<?php echo $val; ?>><?php echo Lang::txt('JYES'); ?></option>
                    </select>
                    <span class="hint"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_APPROVED_HINT'); ?></span>
                </div>

                <div
                    class="input-wrap"
                    data-hint="<?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW_HINT'); ?>">
                    <label for="param-emailnew">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW'); ?>
                    </label>
                    <select name="params[emailnew]" id="param-emailnew">
                        <?php $val = ($this->params->get('emailnew', 0) == 0) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $val; ?>><?php echo Lang::txt('JNO'); ?></option>
                        <?php $val = ($this->params->get('emailnew', 0) == 1) ? ' selected="selected"' : ''; ?>
                        <option value="1"<?php echo $val; ?>><?php echo Lang::txt('JYES'); ?></option>
                    </select>
                    <span class="hint"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_EMAILNEW_HINT'); ?></span>
                </div>
            </fieldset>

            <?php
            $this->view('_fieldmap')
                ->set('import', $this->import)
                ->display();
            ?>
        </div>
        <div class="col span5">
            <?php if ($this->import->get('id')) : ?>
                <table class="meta">
                    <tbody>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_ID'); ?></th>
                            <td><?php echo $this->import->get('id'); ?></td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDBY'); ?></th>
                            <td>
                                <?php
                                if ($created_by = User::getInstance($this->import->get('created_by'))) {
                                    echo $created_by->get('name');
                                }
                                ?>
                            </td>
                        </tr>
                        <tr>
                            <th scope="row"><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_CREATEDON'); ?></th>
                            <td>
                                <time datetime="<?php echo $this->import->get('created_at'); ?>"><?php
                                    echo Date::of($this->import->get('created_at'))->toLocal('m/d/Y @ g:i a');
                                ?></time>
                            </td>
                        </tr>
                    </tbody>
                </table>
            <?php endif; ?>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_UPLOAD'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-file">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATAFILEUPLOAD'); ?>
                    </label>
                    <input type="file" name="file" id="field-file" />
                </div>
            </fieldset>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELDSET_DATA'); ?></span></legend>

                <?php
                $val = Lang::txt(
                    'COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_HINT',
                    $this->import->fileSpacePath()
                );
                ?>
                <div class="input-wrap" data-hint="<?php echo $val; ?>">
                    <label for="field-importfile">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE'); ?>
                    </label><br />
                    <select name="import[file]" id="field-importfile">
                        <?php $text = Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_OPTION_NULL'); ?>
                        <option value=""><?php echo $text; ?></option>
                        <?php if (isset($this->files)) : ?>
                            <?php foreach ($this->files as $file) : ?>
                                <?php
                                $file = ltrim($file, DS);
                                $sel = ($this->import->get('file') == $file) ? 'selected="selected"' : ''; ?>
                                <option <?php echo $sel; ?> value="<?php echo $file; ?>"><?php echo $file; ?></option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                    <span class="hint">
                        <?php
                        $val = Lang::txt(
                            'COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_FILE_HINT',
                            $this->import->fileSpacePath()
                        );
                        ?>
                        <?php echo $val; ?>
                    </span>
                </div>

                <?php $val = str_replace('<br />', "\n", Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_HINT')); ?>
                <div class="input-wrap" data-hint="<?php echo $val; ?>">
                    <label for="field-importmode">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE'); ?>
                    </label><br />
                    <select name="import[mode]" id="field-importmode">
                        <option value="UPDATE">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_UPDATE'); ?>
                        </option>
                        <option <?php if ($this->import->get('mode') == 'PATCH') {
                            echo 'selected="selected"';
                                } ?> value="PATCH">
                            <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_PATCH'); ?>
                        </option>
                    </select>
                    <span class="hint">
                        <?php echo Lang::txt('COM_MEMBERS_IMPORT_EDIT_FIELD_DATA_MODE_HINT'); ?>
                    </span>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="option" value="<?php echo $this->option ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
    <input type="hidden" name="task" value="save" />
    <input type="hidden" name="import[id]" value="<?php echo $this->import->get('id'); ?>" />

    <?php echo Html::input('token'); ?>
</form>
