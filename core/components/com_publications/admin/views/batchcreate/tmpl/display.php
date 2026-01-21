<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$title = Lang::txt('COM_PUBLICATIONS_PUBLICATIONS')
    . ': ' . Lang::txt('COM_PUBLICATIONS_BATCH_CREATE');
Toolbar::title($title, 'publications');

$this->css('batchcreate');
$this->js('batchcreate');

?>
<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
    . '&task=process'
);
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="batchupload"
    enctype="multipart/form-data"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_IMPORT'); ?></span></legend>

        <div class="grid">
            <div class="col span7">
                <div class="input-wrap">
                    <label for="projectid">
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_ADD_IN_PROJECT'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label>
                    <?php
                    // Draw project list
                    $this->view('_selectprojects')
                         ->set('projects', $this->projects)
                         ->display(); ?>
                </div>
                <div class="input-wrap">
                    <label for="mastertypeid">
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_MASTER_TYPE'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label>
                    <?php
                    // Draw master type list
                    $this->view('_selectmastertypes')
                         ->set('mastertypes', $this->mastertypes)
                         ->display(); ?>
                </div>
                <?php $attachHint = Lang::txt('COM_PUBLICATIONS_FIELD_ATTACH_HINT'); ?>
                <div class="input-wrap file-import" data-hint="<?php echo $attachHint; ?>">
                    <label for="field-file">
                        <?php echo Lang::txt('COM_PUBLICATIONS_FIELD_DATA'); ?>
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label>
                    <input type="file" name="file" id="field-file" />
                </div>
                <div class="input-wrap">
                    <?php $submitLabel = Lang::txt('COM_PUBLICATIONS_UPLOAD_AND_PREPROCESS'); ?>
                    <input
                        type="submit"
                        name="batch_submit"
                        id="batch_submit"
                        value="<?php echo $submitLabel; ?>"
                    />
                </div>
            </div>
            <div class="col span5">
                <?php
                $xsdUrl = Route::url(
                    'index.php?option=' . $this->option
                    . '&controller=' . $this->controller
                    . '&task=xsd'
                );
                $xsdLabel = Lang::txt('COM_PUBLICATIONS_BATCH_XSD');
                ?>
                <p><?php echo Lang::txt('COM_PUBLICATIONS_BATCH_XSD_INSTRUCT'); ?>
                    <a href="<?php echo $xsdUrl; ?>"><?php echo $xsdLabel; ?></a></p>
            </div>
        </div>

        <div class="output-wrap" id="results">
        </div>

        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
        <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
        <input type="hidden" name="task" value="process" />
        <input type="hidden" name="base" value="files" />
        <?php echo Html::input('token'); ?>
    </fieldset>
</form>