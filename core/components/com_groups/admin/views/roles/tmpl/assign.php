<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Request;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;

// No direct access
defined('_HZEXEC_') or die();

$tmpl = Request::getCmd('tmpl', '');

$text = ($this->task == 'edit' ? Lang::txt('COM_GROUPS_EDIT') : Lang::txt('COM_GROUPS_NEW'));

$canDo = \Components\Groups\Helpers\Permissions::getActions('group');

if ($tmpl != 'component') {
    Toolbar::title(Lang::txt('COM_GROUPS') . ': ' . $text, 'groups');
    if ($canDo->get('core.edit')) {
        Toolbar::save();
    }
    Toolbar::cancel();
}

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js('roles.js');
?>
<?php if ($this->getError()) { ?>
    <p class="error"><?php echo implode('<br />', $this->getError()); ?></p>
<?php } ?>
<?php
$formUrl = Route::url('index.php?option=' . $this->option);
$formId = ($tmpl == 'component') ? 'component' : 'item';
$invalidMsg = Lang::txt('COM_GROUPS_ERROR_MISSING_INFORMATION');
$redirectUrl = Route::url(
    'index.php?option=' . $this->option .
    '&controller=membership&gid=' . $this->group->get('cn')
);
?>
<form
    action="<?php echo $formUrl; ?>"
    method="post"
    name="adminForm"
    id="<?php echo $formId; ?>-form"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
    class="editform form-validate"
    data-redirect="<?php echo $redirectUrl; ?>">
<?php if ($tmpl == 'component') { ?>
    <fieldset>
        <div class="configuration">
            <div class="fltrt configuration-options">
                <button type="button" id="btn-save"><?php echo Lang::txt('COM_GROUPS_MEMBER_SAVE'); ?></button>
                <button type="button" id="btn-cancel"><?php echo Lang::txt('COM_GROUPS_MEMBER_CANCEL'); ?></button>
            </div>
            <?php echo Lang::txt('COM_GROUPS_ROLE_ASSIGN') ?>
        </div>
    </fieldset>
<?php } ?>
    <div class="col span12">
        <fieldset class="adminform">
            <legend><span><?php echo Lang::txt('COM_GROUPS_DETAILS'); ?></span></legend>

            <input type="hidden" name="gid" value="<?php echo $this->group->get('cn'); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="controller" value="<?php echo $this->controller; ?>">
            <input type="hidden" name="tmpl" value="<?php echo $tmpl; ?>" />
            <input type="hidden" name="task" value="delegate" />

            <?php
            foreach ($this->ids as $i => $id) {
                ?>
                <input type="hidden" name="id[<?php echo $i; ?>]" value="<?php echo $id; ?>" />
                <?php
            }
            ?>

            <div class="input-wrap">
                <?php $v1 = Lang::txt('COM_GROUPS_ROLE_CHOOSE'); ?>
                <?php $v0 = Lang::txt('JOPTION_REQUIRED'); ?>
                <label for="field-roleid"><?php echo $v1; ?>: <span class="required"><?php echo $v0; ?></span></label>
                <select name="roleid" id="field-roleid" class="required">
                    <option value="0"><?php echo Lang::txt('COM_GROUPS_ROLE_SELECT'); ?></option>
                    <?php foreach ($this->rows as $row) { ?>
                        <?php $val = $this->escape($row->get('name')); ?>
                        <option value="<?php echo $row->get('id'); ?>"><?php echo $val; ?></option>
                    <?php } ?>
                </select>
            </div>
        </fieldset>
    </div>

    <?php echo Html::input('token'); ?>
</form>