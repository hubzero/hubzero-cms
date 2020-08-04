<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

// Get the permissions helper
$canDo = Components\Installer\Admin\Helpers\Installer::getActions();

// Title & toolbar
$text = ($this->task == 'edit' ? Lang::txt('JACTION_EDIT') : Lang::txt('JACTION_CREATE'));

Toolbar::title(Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADER') . ': ' . $text);
if ($canDo->get('core.edit'))
{
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<?php if ($this->getErrors()) { ?>
		<p class="error"><?php echo implode('<br />', $this->getErrors()); ?></p>
	<?php } ?>
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('JDETAILS'); ?></span></legend>

				<div class="input-wrap">
					<label for="field-url"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_URL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[url]" id="field-url" maxlength="250" value="<?php echo $this->escape(stripslashes($this->row->get('url'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_NAME'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[name]" id="field-name" maxlength="250" class="required" value="<?php echo $this->escape(stripslashes($this->row->get('name'))); ?>" />
				</div>
				<div class="input-wrap">
					<label for="field-alias"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_ALIAS'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
					<input type="text" name="fields[alias]" id="field-alias" maxlength="250" class="required" value="<?php echo $this->escape(stripslashes($this->row->get('alias'))); ?>" />
				</div>

				<div class="input-wrap">
					<label for="field-type"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_TYPE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label><br />
                    <select name="fields[type]" class="required" id="field-type">
                        <option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_TYPE_SELECT');?></option>
                        <?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::TypeOptions(), 'value', 'text', $this->row['type'], true);?>
                    </select>
				</div>

   				<div class="input-wrap">
					<label for="field-client_id"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_CLIENT_ID'); ?>: </label><br />
					<select name="fields[client_id]" id="field-client_id">
                        <option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_CLIENT_SELECT');?></option>
                        <?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::LocationOptions(), 'value', 'text', $this->row['client_id'], true);?>
					</select>
				</div>
				<div class="input-wrap">
					<label for="field-folder"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_FOLDER'); ?>: </label><br />
					<select name="fields[folder]" id="field-folder">
                        <option value=""><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_VALUE_FOLDER_SELECT');?></option>
                        <?php echo Html::select('options', Components\Installer\Admin\Helpers\Installer::GroupOptions(), 'value', 'text', $this->row['group']);?>
					</select>
				</div>

  				<div class="input-wrap">
					<label for="field-description"><?php echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_HEADING_DESCRIPTION'); ?>: </label>
					<textarea name="fields[description]" id="field-description" rows="10"><?php echo $this->escape($this->row->get('description')); ?></textarea>
				</div>

<!--
				<div class="input-wrap">
					<label for="field-apikey"><?php //echo Lang::txt('COM_INSTALLER_CUSTOMEXTS_EDIT_REPO_APIKEY'); ?>: </label><br />
					<input type="text" name="fields[apikey]" id="field-apikey" maxlength="250" value="<?php //echo $this->escape(stripslashes($this->row->get('apikey'))); ?>" />
				</div>
-->

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
                            <time datetime="<?php echo $this->escape($this->row->created); ?>"><?php echo $this->escape(Date::of($this->row->created)->toLocal()); ?></time>
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
                            $modifier = User::getInstance($this->row->created_by);
                            echo $this->escape($modifier->get('name', Lang::txt('COM_PLUGINS_UNKNOWN')) . ' (' . $this->row->created_by . ')');
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
                            <time datetime="<?php echo $this->escape($this->row->modified); ?>"><?php echo $this->escape(Date::of($this->row->modified)->toLocal()); ?></time>
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
                            echo $this->escape($modifier->get('name', Lang::txt('COM_PLUGINS_UNKNOWN')) . ' (' . $this->row->modified_by . ')');
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