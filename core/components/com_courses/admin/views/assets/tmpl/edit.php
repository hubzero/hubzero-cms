<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// phpcs:disable Generic.Files.LineLength

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Courses\Helpers\Permissions::getActions();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));
if (!$this->tmpl) {
    Toolbar::title(Lang::txt('COM_COURSES') . ': ' . Lang::txt('COM_COURSES_ASSETS') . ': ' . $text, 'courses.png');
    if ($canDo->get('core.edit')) {
        Toolbar::save();
    }
    Toolbar::cancel();
}

Html::behavior('framework', true);
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

if ($this->row->get('id')) {
    $id = $this->row->get('id');
} else {
    $id = 'tmp' . time() . rand(0, 10000);
}

// The attribute that marks the current value in the form fields below
$typeSelected = function ($value) {
    if ($this->row->get('type') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$subtypeSelected = function ($value) {
    if ($this->row->get('subtype') == $value) {
        return ' selected="selected"';
    }

    return '';
};

$stateSelected = function ($value) {
    if ($this->row->get('state') == $value) {
        return ' selected="selected"';
    }

    return '';
};
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
<?php } ?>
<form action="<?= Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller) ?>" method="post"
    name="adminForm" id="<?= ($this->tmpl == 'component') ? 'component-form' : 'item-form' ?>"
    enctype="multipart/form-data" class="editform form-validate"
    data-invalid-msg="<?= $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')) ?>">
<?php if ($this->tmpl == 'component') { ?>
    <fieldset>
        <div class="configuration">
            <div class="configuration-options">
                <button type="button" id="btn-save"><?php echo Lang::txt('COM_COURSES_SAVE'); ?></button>
                <button type="button" id="btn-cancel"><?php echo Lang::txt('JCANCEL'); ?></button>
            </div>
            <?php echo $text; ?>
        </div>
    </fieldset>
<?php } ?>
    <div class="col span12">
        <fieldset class="adminform">
            <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

            <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
            <input type="hidden" name="fields[course_id]" value="<?php echo $this->escape($this->course_id); ?>" />
            <input type="hidden" name="fields[scope]" value="<?php echo $this->escape($this->scope); ?>" />
            <input type="hidden" name="fields[scope_id]" value="<?php echo $this->escape($this->scope_id); ?>" />

            <input type="hidden" name="fields[lid]" value="<?php echo $this->escape($id); ?>" />

            <input type="hidden" name="tmpl" value="<?php echo $this->escape($this->tmpl); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->escape($this->option); ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->escape($this->controller); ?>">
            <input type="hidden" name="task" value="save" />

            <div class="grid">
                <div class="col span6">
                    <div class="input-wrap">
                        <label for="field-type"><?php echo Lang::txt('COM_COURSES_FIELD_TYPE'); ?>:</label>
                        <select name="fields[type]" id="field-type">
                            <option value="video"<?= $typeSelected('video') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO') ?></option>
                            <option value="file"<?= $typeSelected('file') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_FILE') ?></option>
                            <option value="form"<?= $typeSelected('form') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_FORM') ?></option>
                            <option value="text"<?= $typeSelected('text') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_TEXT') ?></option>
                            <option value="url"<?= $typeSelected('url') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_URL') ?></option>
                        </select>
                    </div>
                </div>
                <div class="col span6">
                    <div class="input-wrap">
                        <label for="field-subtype"><?php echo Lang::txt('COM_COURSES_FIELD_SUBTYPE'); ?>:</label>
                        <select name="fields[subtype]" id="field-subtype">
                            <option value="video"<?= $subtypeSelected('video') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_VIDEO') ?></option>
                            <option value="embedded"<?= $subtypeSelected('embedded') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_EMBEDDED') ?></option>
                            <option value="file"<?= $subtypeSelected('file') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_FILE') ?></option>
                            <option value="exam"<?= $subtypeSelected('exam') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_EXAM') ?></option>
                            <option value="quiz"<?= $subtypeSelected('quiz') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_QUIZ') ?></option>
                            <option value="homework"<?= $subtypeSelected('homework') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_HOMEWORK') ?></option>
                            <option value="note"<?= $subtypeSelected('note') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_NOTE') ?></option>
                            <option value="wiki"<?= $subtypeSelected('wiki') ?>><?= Lang::txt('COM_COURSES_ASSET_TYPE_WIKI') ?></option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="input-wrap">
                <label for="field-state"><?php echo Lang::txt('COM_COURSES_FIELD_STATE'); ?>:</label>
                <select name="fields[state]" id="field-state">
                    <option value="0"<?= $stateSelected(0) ?>><?= Lang::txt('COM_COURSES_UNPUBLISHED') ?></option>
                    <option value="1"<?= $stateSelected(1) ?>><?= Lang::txt('COM_COURSES_PUBLISHED') ?></option>
                    <option value="2"<?= $stateSelected(2) ?>><?= Lang::txt('COM_COURSES_TRASHED') ?></option>
                </select>
            </div>
            <div class="input-wrap">
                <label for="field-title"><?= Lang::txt('COM_COURSES_FIELD_TITLE') ?>:  <span
                    class="required"><?= Lang::txt('JOPTION_REQUIRED') ?></span></label>
                <input type="text" name="fields[title]" id="field-title" class="required"
                    value="<?= $this->escape(stripslashes($this->row->get('title'))) ?>" size="50" />
            </div>
            <div class="input-wrap">
                <label for="field-url"><?php echo Lang::txt('COM_COURSES_FIELD_URL'); ?>:</label>
                <input type="text" name="fields[url]" id="field-url"
                    value="<?= $this->escape(stripslashes($this->row->get('url'))) ?>" size="50" />
            </div>
            <div class="input-wrap">
                <label for="field-content"><?php echo Lang::txt('COM_COURSES_FIELD_CONTENT'); ?>:</label>
                <textarea name="fields[content]" id="field-content" rows="4"
                    cols="35"><?= $this->escape(stripslashes($this->row->get('content'))) ?></textarea>
            </div>

            <?php
            $sourceUrl = Route::url(
                'index.php?option=' . $this->option . '&controller=media&tmpl=component&listdir=' . $id . '&course='
                    . $this->escape($this->course_id),
            );
            ?>
            <iframe width="100%" height="225" name="filelist" id="filelist" frameborder="0" src="<?= $sourceUrl ?>"></iframe>
        </fieldset>
    </div>

    <?php echo Html::input('token'); ?>
</form>
