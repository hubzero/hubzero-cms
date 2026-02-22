<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$canDo = \Components\Wiki\Helpers\Permissions::getActions('comment');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

$toolbarTitle = Lang::txt('COM_WIKI') . ': ' . Lang::txt('COM_WIKI_PAGE')
    . ': ' . Lang::txt('COM_WIKI_COMMENTS') . ': ' . $text;
Toolbar::title($toolbarTitle, 'wiki');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('comment');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form action="<?php echo $formAction; ?>"
    method="post" name="adminForm" id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <?php $chkAnon = $this->row->get('anonymous') ? ' checked="checked"' : ''; ?>
                    <input class="option" type="checkbox" name="fields[anonymous]" id="field-anonymous"
                        value="1"<?php echo $chkAnon; ?> />
                    <label for="field-anonymous"><?php echo Lang::txt('COM_WIKI_FIELD_ANONYMOUS'); ?></label>
                </div>

                <div class="input-wrap">
                    <label for="field-ctext">
                        <?php echo Lang::txt('COM_WIKI_FIELD_CONTENT'); ?>
                        <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span>
                    </label><br />
                    <textarea name="fields[ctext]" id="field-ctext" class="required" cols="35" rows="15"><?php
                        echo $this->escape(stripslashes($this->row->get('ctext', '')));
                    ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th><?php echo Lang::txt('COM_WIKI_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->creator->get('name'));
                            ?>
                            <input type="hidden" name="fields[created_by]" id="field-created_by"
                                value="<?php echo $this->escape($this->row->get('created_by')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_WIKI_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo $this->row->get('created'); ?>
                            <input type="hidden" name="fields[created]" id="field-created"
                                value="<?php echo $this->escape($this->row->get('created')); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th><?php echo Lang::txt('COM_WIKI_FIELD_PAGE'); ?>:</th>
                        <td>
                            <?php echo $this->row->get('page_id'); ?>
                            <input type="hidden" name="fields[page_id]" id="field-page_id"
                                value="<?php echo $this->escape($this->row->get('page_id')); ?>" />
                            <input type="hidden" name="page_id"
                                value="<?php echo $this->escape($this->row->get('page_id')); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <input type="hidden" name="fields[parent]" value="<?php echo $this->escape($this->row->get('parent')); ?>" />
    <input type="hidden" name="fields[id]" value="<?php echo $this->escape($this->row->get('id')); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
