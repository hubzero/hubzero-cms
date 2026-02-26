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

// No direct access
defined('_HZEXEC_') or die();

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(
    Lang::txt('COM_BILLBOARDS_MANAGER') . ': '
    . Lang::txt('COM_BILLBOARDS_COLLECTIONS') . ': ' . $text,
    'billboards'
);
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('collection');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<?php $invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED')); ?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <fieldset class="adminform">
        <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

        <div class="input-wrap">
            <label for="field-name">
                <?php echo Lang::txt('COM_BILLBOARDS_FIELD_COLLECTION_NAME'); ?>:
                <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
            </label><br />
            <?php $nameVal = $this->escape(stripslashes(
                $this->row->name == null ? '' : $this->row->name
            )); ?>
            <input type="text"
                name="name"
                id="field-name"
                class="required"
                value="<?php echo $nameVal; ?>"
                size="50"
            />
        </div>
    </fieldset>
    <input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
