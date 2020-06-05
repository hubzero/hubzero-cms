<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$isNew = empty($this->item->lang_id);
$canDo = Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt($isNew ? 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE' : 'COM_LANGUAGES_VIEW_LANGUAGE_EDIT_EDIT_TITLE'), 'langmanager');

// If a new item, can save.
if ($isNew && $canDo->get('core.create'))
{
	Toolbar::save();
}

//If an existing item, allow to Apply and Save.
if (!$isNew && $canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

// If an existing item, can save to a copy only if we have create rights.
if ($canDo->get('core.create'))
{
	Toolbar::save2new();
}

if ($isNew)
{
	Toolbar::cancel();
}
else
{
	Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}

Toolbar::divider();
Toolbar::help('language');

Html::behavior('tooltip');
Html::behavior('formvalidation');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&lang_id=' . (int) $this->item->lang_id); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<?php if ($this->item->lang_id) : ?>
					<legend><span><?php echo Lang::txt('JGLOBAL_RECORD_NUMBER', $this->item->lang_id); ?></span></legend>
				<?php else : ?>
					<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_LANGUAGE_EDIT_NEW_TITLE'); ?></span></legend>
				<?php endif; ?>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_TITLE_DESC'); ?>">
					<label for="field-title"><?php echo Lang::txt('JGLOBAL_TITLE'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title]" id="field-title" class="required" maxlength="50" value="<?php echo $this->escape($this->item->title); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_TITLE_NATIVE_DESC'); ?>">
					<label for="field-title"><?php echo Lang::txt('COM_LANGUAGES_FIELD_TITLE_NATIVE_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[title_native]" id="field-title_native" class="required" maxlength="50" value="<?php echo $this->escape($this->item->title_native); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_CODE_DESC'); ?>">
					<label for="field-sef"><?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_CODE_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[sef]" id="field-sef" class="required" maxlength="7" value="<?php echo $this->escape($this->item->sef); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_IMAGE_DESC'); ?>">
					<label for="field-image"><?php echo Lang::txt('COM_LANGUAGES_FIELD_IMAGE_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[image]" id="field-image" class="required" maxlength="7" value="<?php echo $this->escape($this->item->image); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_DESC'); ?>">
					<label for="field-lang_code"><?php echo Lang::txt('COM_LANGUAGES_FIELD_LANG_TAG_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[lang_code]" id="field-lang_code" class="required" maxlength="7" value="<?php echo $this->escape($this->item->lang_code); ?>" />
				</div>

				<?php if ($canDo->get('core.edit.state')) : ?>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_PUBLISHED_DESC'); ?>">
						<label for="field-published"><?php echo Lang::txt('JSTATUS'); ?>:</label>
						<select name="fields[published]" id="field-published">
							<option value="0"<?php echo ($this->item->published == 0) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JUNPUBLISHED'); ?></option>
							<option value="1"<?php echo ($this->item->published == 1) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JPUBLISHED'); ?></option>
							<option value="-2"<?php echo ($this->item->published == -2) ? ' selected="selected"' : ''; ?>><?php echo Lang::txt('JTRASHED'); ?></option>
						</select>
					</div>
				<?php endif; ?>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('JFIELD_ACCESS_DESC'); ?>">
					<label for="field-access"><?php echo Lang::txt('JFIELD_ACCESS_LABEL'); ?>:</label>
					<select name="fields[access]" id="field-access">
						<?php echo Html::select('options', Html::access('assetgroups'), 'value', 'text', $this->item->access); ?>
					</select>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_DESCRIPTION_DESC'); ?>">
					<label for="field-description"><?php echo Lang::txt('JGLOBAL_DESCRIPTION'); ?>:</label>
					<textarea name="fields[description]" id="field-description" rows="5" cols="80"><?php echo $this->escape($this->item->description); ?></textarea>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('JGLOBAL_FIELD_ID_DESC'); ?>">
					<label for="field-lang_id"><?php echo Lang::txt('JGLOBAL_FIELD_ID_LABEL'); ?>:</label>
					<input type="text" name="fields[lang_id]" id="field-lang_id" readonly="readonly" value="<?php echo $this->escape($this->item->lang_id); ?>" />
				</div>
			</fieldset>
		</div>
		<div class="col span5">
			<?php echo Html::sliders('start', 'language-sliders-' . $this->item->lang_code, array('useCookie' => 1)); ?>

			<?php echo Html::sliders('panel', Lang::txt('JGLOBAL_FIELDSET_METADATA_OPTIONS'), 'metadata'); ?>
				<fieldset class="panelform">
					<div class="input-wrap" data-hint="<?php echo Lang::txt('JFIELD_META_KEYWORDS_DESC'); ?>">
						<label for="field-metakey"><?php echo Lang::txt('JFIELD_META_KEYWORDS_LABEL'); ?>:</label>
						<textarea name="fields[metakey]" id="field-metakey" rows="3" cols="30"><?php echo $this->escape($this->item->metakey); ?></textarea>
					</div>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('JFIELD_META_DESCRIPTION_DESC'); ?>">
						<label for="field-metakey"><?php echo Lang::txt('JFIELD_META_DESCRIPTION_LABEL'); ?>:</label>
						<textarea name="fields[metakey]" id="field-metakey" rows="3" cols="30"><?php echo $this->escape($this->item->metakey); ?></textarea>
					</div>
				</fieldset>

			<?php echo Html::sliders('panel', Lang::txt('COM_LANGUAGES_FIELDSET_SITE_NAME_LABEL'), 'site_name'); ?>
				<fieldset class="panelform">
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_FIELD_SITE_NAME_DESC'); ?>">
						<label for="field-sitename"><?php echo Lang::txt('COM_LANGUAGES_FIELD_SITE_NAME_LABEL'); ?>:</label>
						<input type="text" name="fields[sitename]" id="field-sitename" size="50" value="<?php echo $this->escape($this->item->sitename); ?>" />
					</div>
				</fieldset>

			<?php echo Html::sliders('end'); ?>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<?php echo Html::input('token'); ?>
		</div>
	</div>
</form>
