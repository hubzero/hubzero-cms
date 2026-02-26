<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

use Hubzero\Facades\Date;
use Hubzero\Facades\Html;
use Hubzero\Facades\Lang;
use Hubzero\Facades\Route;
use Hubzero\Facades\Toolbar;
use Hubzero\Facades\User;

// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = \Components\Installer\Admin\Helpers\Installer::getActions();

// Title & toolbar
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER') . ': ' . $text);
if ($canDo->get('core.edit')) {
    Toolbar::save();
    Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');
Html::behavior('tooltip');

$this->js();
?>

<?php
$formAction = Route::url('index.php?option=' . $this->option);
$invalidMsg = $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));
?>
<form action="<?php echo $formAction; ?>"
    method="post"
    name="adminForm"
    id="item-form"
    class="editform form-validate"
    data-invalid-msg="<?php echo $invalidMsg; ?>"
>
    <?php if ($this->getErrors()) { ?>
        <p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
    <?php } ?>
    <div class="grid">
        <div class="col span7">
            <fieldset class="adminform">
                <legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('url'); ?><br />
                    <?php $urlVal = $this->escape(stripslashes($this->row->get('url'))); ?>
                    <input type="text"
                        name="fields[url]"
                        id="field-url"
                        maxlength="250"
                        class="required"
                        value="<?php echo $urlVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('name'); ?><br />
                    <?php $nameVal = $this->escape(stripslashes($this->row->get('name'))); ?>
                    <input type="text"
                        name="fields[name]"
                        id="field-name"
                        maxlength="250"
                        class="required"
                        value="<?php echo $nameVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('alias'); ?><br />
                    <?php $aliasVal = $this->escape(stripslashes($this->row->get('alias'))); ?>
                    <input type="text"
                        name="fields[alias]"
                        id="field-alias"
                        maxlength="250"
                        class="required"
                        value="<?php echo $aliasVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('type'); ?><br />
                    <select name="fields[type]" class="required" id="field-type">
                        <?php $typeLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT'); ?>
                        <option value=""><?php echo $typeLabel; ?></option>
                        <?php
                        $helpers = '\Components\Installer\Admin\Helpers\Installer';
                        echo Html::select(
                            'options',
                            $helpers::TypeOptions(),
                            'value',
                            'text',
                            $this->row['type'],
                            true
                        );
                        ?>
                    </select>
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('folder'); ?><br />
                    <select name="fields[folder]" id="field-folder">
                        <?php $folderLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT'); ?>
                        <option value=""><?php echo $folderLabel; ?></option>
                        <?php echo Html::select(
                            'options',
                            $helpers::GroupOptions(),
                            'value',
                            'text',
                            $this->row['folder']
                        ); ?>
                    </select>
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('description'); ?><br />
                    <?php $descVal = $this->escape($this->row->get('description')); ?>
                    <textarea name="fields[description]"
                        id="field-description"
                        rows="5"
                    ><?php echo $descVal; ?></textarea>
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('apikey'); ?><br />
                    <?php $apikeyVal = $this->escape(stripslashes($this->row->get('apikey'))); ?>
                    <input type="text"
                        name="fields[apikey]"
                        id="field-apikey"
                        maxlength="250"
                        value="<?php echo $apikeyVal; ?>"
                    />
                </div>

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('git_branch'); ?><br />
                    <?php $branchVal = $this->escape(stripslashes($this->row->get('git_branch'))); ?>
                    <input type="text"
                        name="fields[git_branch]"
                        id="field-git_branch"
                        maxlength="250"
                        value="<?php echo $branchVal; ?>"
                    />
                </div>

                <!--
                <div class="input-wrap">
                    <?php //echo $this->form->getLabel('git_tag'); ?><br />
                    <input type="text"
                        name="fields[git_tag]"
                        id="field-git_tag"
                        maxlength="250"
                        value=""
                    />
                </div>
                -->

                <div class="input-wrap">
                    <?php echo $this->form->getLabel('client_id'); ?><br />
                    <select name="fields[client_id]" id="field-client_id">
                        <?php $clientLabel = Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT'); ?>
                        <option value=""><?php echo $clientLabel; ?></option>
                        <?php echo Html::select(
                            'options',
                            $helpers::LocationOptions(),
                            'value',
                            'text',
                            $this->row['client_id'],
                            true
                        ); ?>
                    </select>
                </div>

            </fieldset>
        </div>
        <div class="col span5">

            <table class="meta">
                <tbody>

                    <?php if ($this->row->created && $this->row->created != '0000-00-00 00:00:00') : ?>
                    <tr>
                        <th>
                            <?php echo Lang::txt('JGLOBAL_FIELD_CREATED_LABEL'); ?>
                        </th>
                        <td>
                            <?php
                            $created = $this->escape($this->row->created);
                            $createdLocal = $this->escape(Date::of($this->row->created)->toLocal());
                            ?>
                            <time datetime="<?php echo $created; ?>">
                                <?php echo $createdLocal; ?>
                            </time>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($this->row->created_by) : ?>
                    <tr>
                        <th>
                            <?php echo Lang::txt('JGLOBAL_FIELD_CREATED_BY_LABEL'); ?>
                        </th>
                        <td>
                            <?php
                            $unknownTxt = Lang::txt('COM_PLUGINS_UNKNOWN');
                            $modifier = User::getInstance($this->row->created_by);
                            $modName = $modifier->get('name', $unknownTxt);
                            echo $this->escape($modName . ' (' . $this->row->created_by . ')');
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>


                    <?php if ($this->row->modified && $this->row->modified != '0000-00-00 00:00:00') : ?>
                    <tr>
                        <th>
                            <?php echo Lang::txt('JGLOBAL_FIELD_MODIFIED_LABEL'); ?>
                        </th>
                        <td>
                            <?php
                            $modified = $this->escape($this->row->modified);
                            $modifiedLocal = $this->escape(Date::of($this->row->modified)->toLocal());
                            ?>
                            <time datetime="<?php echo $modified; ?>">
                                <?php echo $modifiedLocal; ?>
                            </time>
                        </td>
                    </tr>
                    <?php endif; ?>
                    <?php if ($this->row->modified_by) : ?>
                    <tr>
                        <th>
                            <?php echo Lang::txt('JGLOBAL_FIELD_MODIFIED_BY_LABEL'); ?>
                        </th>
                        <td>
                            <?php
                            $modifier = User::getInstance($this->row->modified_by);
                            $modName = $modifier->get('name', $unknownTxt);
                            echo $this->escape($modName . ' (' . $this->row->modified_by . ')');
                            ?>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>

        </div>
    </div>
    <input type="hidden" name="fields[extension_id]" value="<?php echo $this->row->get('extension_id'); ?>" />
    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
    <input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
    <input type="hidden" name="task" value="save" />

    <?php echo Html::input('token'); ?>
</form>