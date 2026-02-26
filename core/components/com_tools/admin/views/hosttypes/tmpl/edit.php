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

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('New Host'));

Toolbar::title(Lang::txt('COM_TOOLS') . ': ' . Lang::txt('COM_TOOLS_HOST_TYPES') . ': ' . $text, 'tools');
Toolbar::save();
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('hosttype');
?>

<?php $formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>
<form action="<?php echo $formAction; ?>" method="post" name="adminForm" id="item-form">
    <?php if ($this->getErrors()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
    <div class="grid">
        <div class="col span6">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-name"><?php echo Lang::txt('COM_TOOLS_FIELD_NAME'); ?>:</label><br />
                    <?php $nameVal = $this->escape(
                        stripslashes($this->row->name == null ? '' : $this->row->name)
                    ); ?>
                    <input type="text" name="fields[name]" id="field-name"
                        size="30" maxlength="255" value="<?php echo $nameVal; ?>" />
                </div>

                <div class="input-wrap">
                    <label for="field-value"><?php echo Lang::txt('COM_TOOLS_FIELD_VALUE'); ?>:</label><br />
                    <?php $valueVal = $this->escape(
                        stripslashes($this->row->value == null ? '' : $this->row->value)
                    ); ?>
                    <input type="text" name="fields[value]" id="field-value"
                        size="30" maxlength="255" value="<?php echo $valueVal; ?>" />
                </div>

                <div class="input-wrap">
                    <label for="field-description">
                        <?php echo Lang::txt('COM_TOOLS_FIELD_DESCRIPTION'); ?>:</label><br />
                    <?php $descVal = $this->escape(
                        stripslashes($this->row->description == null ? '' : $this->row->description)
                    ); ?>
                    <input type="text" name="fields[description]" id="field-description"
                        size="30" maxlength="255" value="<?php echo $descVal; ?>" />
                </div>
            </fieldset>
        </div>
        <div class="col span6">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_BIT'); ?></th>
                        <td><?php echo $this->escape($this->bit); ?></td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_TOOLS_COL_REFERENCES'); ?></th>
                        <td><?php echo $this->escape($this->refs); ?></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <?php $statusVal = (isset($this->status)) ? $this->status : 'new'; ?>
    <input type="hidden" name="fields[status]" value="<?php echo $statusVal; ?>" />
    <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->name); ?>" />

    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
