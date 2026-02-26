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

$canDo = \Components\Forum\Helpers\Permissions::getActions('section');

$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_FORUM') . ': ' . Lang::txt('COM_FORUM_CATEGORIES') . ': ' . $text, 'forum');
Toolbar::spacer();
if ($canDo->get('core.edit')) {
    Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('category');

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
                            <?php $scopeLabel = Lang::txt('COM_FORUM_FIELD_SCOPE'); ?>
                            <label for="field-scope]"><?php echo $scopeLabel; ?>:</label><br />
                            <?php $scopeVal = $this->escape(stripslashes($this->row->scope)); ?>
                            <input type="text"
                                name="fields[scope]"
                                id="field-scope]"
                                maxlength="250"
                                value="<?php echo $scopeVal; ?>" />
                        </div>
                    </div>
                    <div class="col span6">
                        <div class="input-wrap">
                            <?php $scopeIdLabel = Lang::txt('COM_FORUM_FIELD_SCOPE_ID'); ?>
                            <label for="field-scope_id"><?php echo $scopeIdLabel; ?>:</label><br />
                            <?php $scopeIdVal = $this->escape(stripslashes($this->row->scope_id)); ?>
                            <input type="text"
                                name="fields[scope_id]"
                                id="field-scope_id"
                                maxlength="250"
                                value="<?php echo $scopeIdVal; ?>" />
                        </div>
                    </div>
                </div>

                <div class="input-wrap">
                    <?php $sectionLabel = Lang::txt('COM_FORUM_FIELD_SECTION'); ?>
                    <?php $requiredTxt = Lang::txt('JOPTION_REQUIRED'); ?>
                    <label for="field-section_id">
                        <?php echo $sectionLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <select name="fields[section_id]" id="field-section_id" class="required">
                        <option value="-1"><?php echo Lang::txt('COM_FORUM_FIELD_SECTION_SELECT'); ?></option>
                        <?php foreach ($this->sections as $group => $sections) { ?>
                            <optgroup label="<?php echo $this->escape(stripslashes($group)); ?>">
                                <?php foreach ($sections as $section) { ?>
                                    <?php
                                    $selected = ($this->row->section_id == $section->id)
                                        ? ' selected="selected"'
                                        : '';
                                    ?>
                                    <option value="<?php echo $section->id; ?>"<?php echo $selected; ?>>
                                        <?php echo $this->escape(stripslashes($section->title)); ?>
                                    </option>
                                <?php } ?>
                            </optgroup>
                        <?php } ?>
                    </select>
                </div>

                <div class="input-wrap">
                    <?php $titleLabel = Lang::txt('COM_FORUM_FIELD_TITLE'); ?>
                    <label for="field-title">
                        <?php echo $titleLabel; ?>: <span class="required"><?php echo $requiredTxt; ?></span>
                    </label><br />
                    <?php $titleVal = $this->escape(stripslashes($this->row->title)); ?>
                    <input type="text"
                        name="fields[title]"
                        id="field-title"
                        class="required"
                        maxlength="250"
                        value="<?php echo $titleVal; ?>" />
                </div>

                <div class="input-wrap" data-hint="<?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?>">
                    <label for="field-alias"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS'); ?>:</label><br />
                    <?php $aliasVal = $this->escape(stripslashes($this->row->alias)); ?>
                    <input type="text"
                        name="fields[alias]"
                        id="field-alias"
                        maxlength="250"
                        value="<?php echo $aliasVal; ?>" />
                    <span class="hint"><?php echo Lang::txt('COM_FORUM_FIELD_ALIAS_HINT'); ?></span>
                </div>

                <div class="input-wrap">
                    <label for="field-description"><?php echo Lang::txt('COM_FORUM_FIELD_DESCRIPTION'); ?></label><br />
                    <?php $descVal = $this->escape(stripslashes($this->row->description)); ?>
                    <textarea name="fields[description]"
                        id="field-description"
                        cols="35"
                        rows="5"><?php echo $descVal; ?></textarea>
                </div>
            </fieldset>
        </div>
        <div class="col span5">
            <table class="meta">
                <tbody>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATOR'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->creator->get('name'));
                            ?>
                            <?php $createdByVal = $this->escape($this->row->created_by); ?>
                            <input type="hidden"
                                name="fields[created_by]"
                                id="field-created_by"
                                value="<?php echo $createdByVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_CREATED'); ?>:</th>
                        <td>
                            <?php echo Date::of($this->row->get('created'))->toLocal(); ?>
                            <?php $createdVal = $this->escape($this->row->created_time); ?>
                            <input type="hidden"
                                name="fields[created]"
                                id="field-created"
                                value="<?php echo $createdVal; ?>" />
                        </td>
                    </tr>
                <?php if ($this->row->modified_by) { ?>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIER'); ?>:</th>
                        <td>
                            <?php
                            echo $this->escape($this->row->modifier->get('name'));
                            ?>
                            <?php $modByVal = $this->escape($this->row->modified_by); ?>
                            <input type="hidden"
                                name="fields[modified_by]"
                                id="field-modified_by"
                                value="<?php echo $modByVal; ?>" />
                        </td>
                    </tr>
                    <tr>
                        <th scope="row"><?php echo Lang::txt('COM_FORUM_FIELD_MODIFIED'); ?>:</th>
                        <td>
                            <?php echo Date::of($this->row->get('modified'))->toLocal(); ?>
                            <?php $modVal = $this->escape($this->row->modified); ?>
                            <input type="hidden"
                                name="fields[modified]"
                                id="field-modified"
                                value="<?php echo $modVal; ?>" />
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JGLOBAL_FIELDSET_PUBLISHING'); ?></span></legend>

                <div class="input-wrap">
                    <?php $closedChecked = $this->row->closed ? ' checked="checked"' : ''; ?>
                    <input class="option"
                        type="checkbox"
                        name="fields[closed]"
                        id="field-closed"
                        value="1"<?php echo $closedChecked; ?> />
                    <label for="field-closed"><?php echo Lang::txt('COM_FORUM_FIELD_CLOSED'); ?></label>
                </div>

                <div class="input-wrap">
                    <label for="field-state"><?php echo Lang::txt('COM_FORUM_FIELD_STATE'); ?>:</label><br />
                    <select name="fields[state]" id="field-state">
                        <?php $sel0 = ($this->row->state == 0) ? ' selected="selected"' : ''; ?>
                        <?php $sel1 = ($this->row->state == 1) ? ' selected="selected"' : ''; ?>
                        <?php $sel2 = ($this->row->state == 2) ? ' selected="selected"' : ''; ?>
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

    <input type="hidden" name="fields[scope]" value="<?php echo $this->row->scope; ?>" />
    <input type="hidden" name="fields[scope_id]" value="<?php echo $this->row->scope_id; ?>" />
    <input type="hidden" name="fields[id]" value="<?php echo $this->row->id; ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>
