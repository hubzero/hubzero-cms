<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Forum\Helpers\Permissions::getActions('section');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_SECTIONS') . ': ' . $text, 'forum');
if ($canDo->get('core.edit')) {
    Toolbar::apply();
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('section');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();

$formAction = Route::url(
    'index.php?option=' . $this->option . '&controller=' . $this->controller
);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>

<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>">
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="grid">
                    <div class="col span6">
                        <div class="input-wrap">
                            <label for="field-scope"><?php echo Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>:</label><br />
                            <?php $scopeVal = $this->escape(stripslashes($this->row->get('scope'))); ?>
                            <input type="text"
                                name="fields[scope]"
                                id="field-scope"
                                size="30"
                                maxlength="250"
                                value="<?php echo $scopeVal; ?>" />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $scopeIdLabel = Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>
                            <label for="field-scope_id"><?php echo $scopeIdLabel; ?>:</label><br />
                            <?php $scopeIdVal = $this->escape(stripslashes($this->row->get('scope_id'))); ?>
                            <input type="text"
                                name="fields[scope_id]"
                                id="field-scope_id"
                                size="30"
                                maxlength="250"
                                value="<?php echo $scopeIdVal; ?>" />
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <?php $titleLabel = Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                    <?php $requiredTxt = Lang::txt('JOPTION_REQUIRED'); ?>
                    <label for="field-title">
                        <?php echo $titleLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <?php $titleVal = $this->escape(stripslashes($this->row->get('title'))); ?>
                    <input type="text"
                        name="fields[title]"
                        id="field-title"
                        class="required"
                        maxlength="250"
                        value="<?php echo $titleVal; ?>" />
                </div>

                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?>">
                    <label for="field-alias"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS'); ?>:</label><br />
                    <?php $aliasVal = $this->escape(stripslashes($this->row->get('alias'))); ?>
                    <input type="text"
                        name="fields[alias]"
                        id="field-alias"
                        maxlength="250"
                        value="<?php echo $aliasVal; ?>" />
                    <span class="hint"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?></span>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo $this->escape($this->row->creator->get('name')); ?>
                            <input type="hidden"
                                name="fields[created_by]"
                                id="field-created_by"
                                value="<?php echo $this->row->get('created_by'); ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php echo Date::of($this->row->get('created'))->toLocal(); ?>
                            <input type="hidden"
                                name="fields[created]"
                                id="field-created"
                                value="<?php echo $this->row->get('created'); ?>" />
                        </td>
                    </tr>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
                    <select name="fields[state]" id="field-state">
                        <?php $sel0 = ($this->row->get('state') == 0) ? ' selected="selected"' : ''; ?>
                        <?php $sel1 = ($this->row->get('state') == 1) ? ' selected="selected"' : ''; ?>
                        <?php $sel2 = ($this->row->get('state') == 2) ? ' selected="selected"' : ''; ?>
                        <option value="0"<?php echo $sel0; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
                        <option value="1"<?php echo $sel1; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
                        <option value="2"<?php echo $sel2; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
                    </select>
                </div>

                <div class="input-wrap">
                    <label for="field-access"><?php echo Lang::txt('COM_FORUM_FIELD_ACCESS'); ?>:</label><br />
                    <select name="fields[access]" id="field-access">
                        <?php echo Html::select(
                            'options',
                            Html::access('assetgroups'),
                            'value',
                            'text',
                            $this->row->get('access')
                        ); ?>
                    </select>
                </div>
            </fieldset>
        </div>
    </div>

    <?php if ($canDo->get('core.admin')) : ?>
        <div class="col span12">
            <fieldset class="panelform">
                <legend><span><?php echo Lang::txt('COM_FORUM_FIELDSET_RULES'); ?></span></legend>
                <?php echo $this->form->getLabel('rules'); ?>
                <?php echo $this->form->getInput('rules'); ?>
            </fieldset>
        </div>
    <?php endif; ?>

    <input type="hidden" name="fields[id]" value="<?php echo $this->row->get('id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
