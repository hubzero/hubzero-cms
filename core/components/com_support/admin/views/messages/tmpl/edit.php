<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Config;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Support\Helpers\Permissions::getActions('message');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$toolbarTitle = Lang::txt('COM_SUPPORT_TICKETS') . ': '
    . Lang::txt('COM_SUPPORT_MESSAGES') . ': ' . $text;
Toolbar::title($toolbarTitle, 'support');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('messages');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('edit.js');
?>

<?php
$formAction = Route::url(
    'index.php?option=' . $this->option
    . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form
    action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('COM_SUPPORT_MESSAGE_LEGEND'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-title">
                        <?php echo Lang::txt('COM_SUPPORT_MESSAGE_SUMMARY'); ?>:
                    </label><br />
                    <input
                        type="text"
                        name="fields[title]"
                        id="field-title"
                        value="<?php echo $this->escape(stripslashes($this->row->get('title'))); ?>"
                    />
                </div>

                <div class="input-wrap">
                    <label for="field-message">
                        <?php echo Lang::txt('COM_SUPPORT_MESSAGE_TEXT'); ?>:
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <textarea
                        name="fields[message]"
                        id="field-message"
                        class="required"
                        cols="35"
                        rows="10"
                    ><?php echo $this->escape(stripslashes($this->row->get('message'))); ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <p><?php echo Lang::txt('COM_SUPPORT_MESSAGE_TEXT_EXPLANATION'); ?></p>
            <dl>
                <dt>{ticket#}</dt>
                <dd><?php echo Lang::txt('COM_SUPPORT_MESSAGE_TICKET_NUM_EXPLANATION'); ?></dd>

                <dt>{sitename}</dt>
                <dd><?php echo Config::get('sitename'); ?></dd>

                <dt>{siteemail}</dt>
                <dd><?php echo Config::get('mailfrom'); ?></dd>
            </dl>
        </div>
    </div>

    <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
