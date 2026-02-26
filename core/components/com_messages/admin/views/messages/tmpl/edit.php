<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Component;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\Event;

// No direct access.
defined('_HZEXEC_') or die();

// Include the HTML helpers.
Html::addIncludePath(Component::path($this->option) . '/helpers/html');
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

Toolbar::title(
    Lang::txt('COM_MESSAGES_WRITE_PRIVATE_MESSAGE'),
    'new-privatemessage.png'
);
Toolbar::save('message.save', 'COM_MESSAGES_TOOLBAR_SEND');
Toolbar::cancel('message.cancel');
Toolbar::help('JHELP_COMPONENTS_MESSAGING_WRITE');

$formAction = Route::url('index.php?option=com_messages');
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
$toHint = Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_DESC');
$toLabel = Lang::txt('COM_MESSAGES_FIELD_USER_ID_TO_LABEL');
$requiredTxt = Lang::txt('JOPTION_REQUIRED');
$subjectHint = Lang::txt('COM_MESSAGES_FIELD_SUBJECT_DESC');
$subjectLabel = Lang::txt('COM_MESSAGES_FIELD_SUBJECT_LABEL');
$messageHint = Lang::txt('COM_MESSAGES_FIELD_MESSAGE_DESC');
$messageLabel = Lang::txt('COM_MESSAGES_FIELD_MESSAGE_LABEL');
?>

<form action="<?php echo $formAction; ?>"
    method="post" name="adminForm" id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>">

    <fieldset class="adminform">
        <div class="input-wrap" data-hint="<?php echo $toHint; ?>">
            <label for="field-user_id_to">
                <?php echo $toLabel; ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
            </label>
            <?php
            $mc = Event::trigger('hubzero.onGetSingleEntry', array(
                array(
                    'members',   // The component to call
                    'fields[user_id_to]',        // Name of the input field
                    'field-user_id_to', // ID of the input field
                    'required',          // CSS class(es) for the input field
                    '' // The value of the input field
                )
            ));
            if (count($mc) > 0) {
                echo $mc[0];
            } else { ?>
                <input type="text" name="fields[user_id_to]"
                    id="field-user_id_to" class="required"
                    value="<?php echo $this->item->get('user_id_to'); ?>" />
            <?php } ?>
        </div>

        <div class="input-wrap"
            data-hint="<?php echo $subjectHint; ?>">
            <label for="field-subject">
                <?php echo $subjectLabel; ?>:
                <span class="required"><?php echo $requiredTxt; ?></span>
            </label>
            <input type="text" name="fields[subject]" id="field-subject"
                maxlength="250" class="required"
                value="<?php echo $this->escape($this->item->get('subject')); ?>" />
        </div>

        <div class="input-wrap"
            data-hint="<?php echo $messageHint; ?>">
            <label for="field-message"><?php echo $messageLabel; ?>:</label>
            <textarea name="message" id="field-message" cols="80" rows="10">
                <?php echo $this->escape($this->item->get('message')); ?>
            </textarea>
        </div>
    </fieldset>

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller"
        value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="" />
    <?php echo Html::input('token'); ?>

</form>
