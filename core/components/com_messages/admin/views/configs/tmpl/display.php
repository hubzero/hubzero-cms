<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;

// No direct access.
defined('_HZEXEC_') or die();

// Include the HTML helpers.
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('config.js');

$formAction = Route::url('index.php?option=com_messages&controller=configs');
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));

$lockChecked = $this->item->get('lock', 0) ? ' checked="checked"' : '';
$mailChecked = $this->item->get('mail_on_new', 1) ? ' checked="checked"' : '';
$purgeHint = Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_DESC');
?>
<form action="<?php echo $formAction; ?>"
    method="post" name="adminForm" id="config-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>">
    <fieldset>
        <div class="configuration">
            <div class="configuration-options">
                <button type="button" id="action_save">
                    <?php echo Lang::txt('JSAVE'); ?>
                </button>
                <button type="button" id="action_cancel">
                    <?php echo Lang::txt('JCANCEL'); ?>
                </button>
            </div>
            <?php echo Lang::txt('COM_MESSAGES_MY_SETTINGS') ?>
        </div>
    </fieldset>

    <fieldset class="adminform">
        <fieldset>
            <legend><?php echo Lang::txt('COM_MESSAGES_FIELD_LOCK_LABEL'); ?></legend>

            <div class="input-wrap">
                <input type="radio" name="lock" id="cfg-lock-yes"
                    value="1"<?php echo $lockChecked; ?> />
                <label for="cfg-lock-yes">
                    <?php echo Lang::txt('JYes'); ?>
                </label>
            </div>

            <div class="input-wrap">
                <input type="radio" name="lock" id="cfg-lock-no"
                    value="0"<?php echo $lockChecked; ?> />
                <label for="cfg-lock-no">
                    <?php echo Lang::txt('JNo'); ?>
                </label>
            </div>
        </fieldset>

        <fieldset>
            <legend>
                <?php echo Lang::txt('COM_MESSAGES_FIELD_MAIL_ON_NEW_LABEL'); ?>
            </legend>

            <div class="input-wrap">
                <input type="radio" name="mail_on_new"
                    id="cfg-mail_on_new-yes"
                    value="1"<?php echo $mailChecked; ?> />
                <label for="cfg-mail_on_new-yes">
                    <?php echo Lang::txt('JYes'); ?>
                </label>
            </div>

            <div class="input-wrap">
                <input type="radio" name="mail_on_new"
                    id="cfg-mail_on_new-no"
                    value="0"<?php echo $mailChecked; ?> />
                <label for="cfg-mail_on_new-no">
                    <?php echo Lang::txt('JNo'); ?>
                </label>
            </div>
        </fieldset>

        <div class="input-wrap"
            data-hint="<?php echo $purgeHint; ?>">
            <?php $purgeLabel = Lang::txt('COM_MESSAGES_FIELD_AUTO_PURGE_LABEL'); ?>
            <label for="cfg-auto_purge"><?php echo $purgeLabel; ?></label>
            <input type="text" name="auto_purge" id="cfg-auto_purge"
                value="<?php echo $this->item->get('auto_purge', 7); ?>" />
            <span class="hint"><?php echo $purgeHint; ?></span>
        </div>

        <input type="hidden" name="option" value="com_messages" />
        <input type="hidden" name="controller" value="configs" />
        <input type="hidden" name="task" value="" />
        <?php echo Html::input('token'); ?>
    </fieldset>
</form>
