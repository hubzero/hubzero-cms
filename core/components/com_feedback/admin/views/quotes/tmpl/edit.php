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

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Feedback\Helpers\Permissions::getActions('quote');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FEEDBACK') . ': ' . $text);
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('quote');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$short_quote = stripslashes($this->row->get('short_quote'));
$miniquote   = stripslashes($this->row->get('miniquote'));
if (!$short_quote) {
    $short_quote =  substr(stripslashes($this->row->get('quote')), 0, 270);
}
if (!$miniquote) {
    $miniquote =  substr(stripslashes($short_quote), 0, 150);
}

if (strlen($short_quote) >= 271) {
    $short_quote = $short_quote . '...';
}
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
    enctype="multipart/form-data"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_FEEDBACK_DETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <?php $notableChecked = ($this->row->get('notable_quote') == 1) ? ' checked="checked"' : ''; ?>
                    <input
                        type="checkbox"
                        name="fields[notable_quote]"
                        id="field-notable_quote"
                        value="1"
                        <?php echo $notableChecked; ?>
                    />
                    <label for="field-notable_quote">
                        <?php echo Lang::txt('COM_FEEDBACK_SELECT_FOR_QUOTES'); ?>
                    </label>
                </div>

                <div class="input-wrap">
                    <?php $reqLabel = Lang::txt('JOPTION_REQUIRED'); ?>
                    <label for="field-fullname">
                        <?php echo Lang::txt('COM_FEEDBACK_FULL_NAME'); ?>:
                        <span class="required"><?php echo $reqLabel; ?></span>
                    </label><br />
                    <?php $fullnameVal = $this->escape(stripslashes($this->row->get('fullname'))); ?>
                    <input
                        type="text"
                        name="fields[fullname]"
                        id="field-fullname"
                        class="required"
                        value="<?php echo $fullnameVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-org">
                        <?php echo Lang::txt('COM_FEEDBACK_ORGANIZATION'); ?>:
                        <span class="required"><?php echo $reqLabel; ?></span>
                    </label><br />
                    <?php $orgVal = $this->escape(stripslashes($this->row->org)); ?>
                    <input
                        type="text"
                        name="fields[org]"
                        id="field-org"
                        class="required"
                        value="<?php echo $orgVal; ?>"
                    />
                </div>

                <?php $userIdHint = Lang::txt('COM_FEEDBACK_USER_ID_EXPLANATION'); ?>
                <div class="input-wrap" data-hint="<?php echo $userIdHint; ?>">
                    <label for="field-user_id">
                        <?php echo Lang::txt('COM_FEEDBACK_USER_ID'); ?>:
                    </label><br />
                    <?php
                    $userIdVal = $this->escape(stripslashes($this->row->get('user_id')));
                    $readonlyAttr = ($this->row->get('id') && $this->row->get('user_id'))
                        ? ' readonly="readonly"' : '';
                    ?>
                    <input
                        type="text"
                        name="fields[user_id]"
                        id="field-user_id"
                        value="<?php echo $userIdVal; ?>"
                        <?php echo $readonlyAttr; ?>
                    />
                    <?php
                    if (!$this->row->get('id')) {
                        echo '<span class="hint">' . $userIdHint . '</span>';
                    }
                    ?>
                </div>

                <fieldset>
                    <legend><?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENTS'); ?>:</legend>

                    <div class="input-wrap">
                        <?php
                        $publishChecked = ($this->row->get('publish_ok') == 1)
                            ? ' checked="checked"' : '';
                        $disabledAttr = $this->row->get('id')
                            ? ' disabled="disabled"' : '';
                        ?>
                        <input
                            type="checkbox"
                            name="fields[publish_ok]"
                            id="publish_ok"
                            value="1"
                            <?php echo $publishChecked . $disabledAttr; ?>
                        />
                        <label for="publish_ok">
                            <?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_PUBLISH'); ?>
                        </label><br />

                        <?php
                        $contactChecked = ($this->row->get('contact_ok') == 1)
                            ? ' checked="checked"' : '';
                        ?>
                        <input
                            type="checkbox"
                            name="fields[contact_ok]"
                            id="contact_ok"
                            value="1"
                            <?php echo $contactChecked . $disabledAttr; ?>
                        />
                        <label for="contact_ok">
                            <?php echo Lang::txt('COM_FEEDBACK_AUTHOR_CONSENT_CONTACT'); ?>
                        </label>
                    </div>
                </fieldset>

                <?php $shortQuoteNote = Lang::txt('COM_FEEDBACK_SHORT_QUOTE_NOTE'); ?>
                <div class="input-wrap" data-hint="<?php echo $shortQuoteNote; ?>">
                    <label for="field-short_quote">
                        <?php echo Lang::txt('COM_FEEDBACK_SHORT_QUOTE'); ?>:
                    </label><br />
                    <?php echo $this->editor('fields[short_quote]', $short_quote, 40, 10, 'field-short_quote'); ?>
                    <span class="hint"><?php echo $shortQuoteNote; ?></span>
                </div>

                <?php $miniquoteHint = Lang::txt('COM_FEEDBACK_MINIQUOTE_HINT'); ?>
                <div class="input-wrap" data-hint="<?php echo $miniquoteHint; ?>">
                    <label for="miniquote">
                        <?php echo Lang::txt('COM_FEEDBACK_MINIQUOTE'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="fields[miniquote]"
                        id="miniquote"
                        value="<?php echo $this->escape($miniquote); ?>"
                        maxlength="150"
                    />
                    <span class="hint"><?php echo $miniquoteHint; ?></span>
                </div>

                <div class="input-wrap">
                    <label for="field-quote">
                        <?php echo Lang::txt('COM_FEEDBACK_FULL_QUOTE'); ?>:
                        <span class="required"><?php echo $reqLabel; ?></span>
                    </label><br />
                    <?php
                    $quoteVal = stripslashes($this->row->get('quote'));
                    echo $this->editor(
                        'fields[quote]',
                        $quoteVal,
                        50,
                        10,
                        'field-quote',
                        array('class' => 'required')
                    );
                    ?>
                </div>

                <div class="input-wrap">
                    <label for="field-date">
                        <?php echo Lang::txt('COM_FEEDBACK_QUOTE_SUBMITTED'); ?>:
                    </label><br />
                    <?php $dateVal = $this->escape($this->row->get('date', Date::toSql())); ?>
                    <input
                        type="text"
                        name="fields[date]"
                        id="field-date"
                        value="<?php echo $dateVal; ?>"
                    />
                </div>

                <?php $notesHint = Lang::txt('COM_FEEDBACK_EDITOR_NOTES_EXPLANATION'); ?>
                <div class="input-wrap" data-hint="<?php echo $notesHint; ?>">
                    <label for="field-notes">
                        <?php echo Lang::txt('COM_FEEDBACK_EDITOR_NOTES'); ?>:
                    </label><br />
                    <?php
                    $notesVal = stripslashes($this->row->get('notes'));
                    echo $this->editor('fields[notes]', $notesVal, 50, 10, 'field-notes');
                    ?>
                    <span class="hint"><?php echo $notesHint; ?></span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_FEEDBACK_PICTURE'); ?></span></legend>

                <div class="input-wrap">
                    <?php
                    $pictures = $this->row->files();

                    foreach ($pictures as $counter => $picture) {
                        list($ow, $oh, $type, $attr) = getimagesize($picture->getPathname());

                        // scale if image is bigger than 120w x120h
                        $num = max($ow / 120, $oh / 120);
                        if ($num > 1) {
                            $mw = round($ow / $num);
                            $mh = round($oh / $num);
                        } else {
                            $mw = $ow;
                            $mh = $oh;
                        }

                        $img = substr($picture->getPathname(), strlen(PATH_ROOT));
                        ?>
                        <div id="picture-<?php echo $counter; ?>">
                            <input
                                type="hidden"
                                name="existingPictures[<?php echo $counter; ?>]"
                                id="existingPictures<?php echo $counter; ?>"
                                value="<?php echo $picture->getFilename(); ?>"
                            />
                            <a class="fancybox-inline" href="<?php echo $img; ?>">
                                <img
                                    src="<?php echo $img; ?>"
                                    height="<?php echo $mh; ?>"
                                    width="<?php echo $mw; ?>"
                                    alt=""
                                />
                            </a>
                            <button
                                type="button"
                                class="delete-image"
                                id="<?php echo $counter; ?>"
                            ><?php echo Lang::txt('COM_FEEDBACK_DELETE'); ?></button>
                        </div>
                        <br />
                        <?php
                    }
                    ?>
                    <input id="imgInp" type="file" name="files[]" multiple="multiple" />
                    <div id="uploadImages"></div>
                </div>
            </fieldset>
        </div>
    </div>

    <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="id" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
