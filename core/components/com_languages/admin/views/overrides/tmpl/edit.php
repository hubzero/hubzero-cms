<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// no direct access
defined('_HZEXEC_') or die();

$canDo = Components\Languages\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_TITLE'), 'langmanager');

if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

// This component does not support Save as Copy

if ($canDo->get('core.edit') && $canDo->get('core.create'))
{
	Toolbar::save2new();
}

if (empty($this->item->key))
{
	Toolbar::cancel();
}
else
{
	Toolbar::cancel('cancel', 'JTOOLBAR_CLOSE');
}
Toolbar::divider();
Toolbar::help('JHELP_EXTENSIONS_LANGUAGE_MANAGER_OVERRIDES_EDIT');

Html::behavior('framework', true);
Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->css('overrider.css')
	->js('overrider.js')
	->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->item->key); ?>" method="post" name="adminForm" id="item-form" class="editform form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>" data-cache_expired="<?php echo (Request::getString('cache_expired')) ? 'expired' : ''; ?>">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo empty($this->item->key) ? Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND') : Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_DESC'); ?>">
					<label for="field-key"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[key]" id="field-key" class="required" size="60" value="<?php echo $this->escape($this->item->key); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_DESC'); ?>">
					<label for="field-override"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_OVERRIDE_LABEL'); ?>:</label>
					<textarea name="fields[override]" id="field-override" rows="5" cols="50"><?php echo $this->escape($this->item->override); ?></textarea>
				</div>

				<?php if ($this->get('client') == 'administrator'): ?>
					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_DESC'); ?>">
						<input type="checkbox" name="fields[both]" id="field-both" value="true" />
						<label for="field-override"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_BOTH_LABEL'); ?>:</label>
					</div>
				<?php endif; ?>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_DESC'); ?>">
					<label for="field-language"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_LANGUAGE_LABEL'); ?>:</label>
					<input type="text" name="fields[language]" id="field-language" size="50" readonly="readonly" value="<?php echo $this->escape($this->item->language); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_DESC'); ?>">
					<label for="field-client"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_CLIENT_LABEL'); ?>:</label>
					<input type="text" name="fields[client]" id="field-client" size="50" readonly="readonly" value="<?php echo $this->escape($this->item->client); ?>" />
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_DESC'); ?>">
					<label for="field-file"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_FILE_LABEL'); ?>:</label>
					<input type="text" name="fields[file]" id="field-file" size="80" readonly="readonly" value="<?php echo $this->escape($this->item->file); ?>" />
				</div>
			</fieldset>
		</div>

		<div class="col span5">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_LEGEND'); ?></span></legend>

				<p><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_TIP'); ?></p>

				<div id="refresh-status" class="overrider-spinner">
					<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_REFRESHING'); ?>
				</div>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_DESC'); ?>">
					<label for="fields_searchtype"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_LABEL'); ?>:</label>
					<fieldset id="fields_searchtype" class="radio inputbox">
						<ul>
							<li>
								<input type="radio" id="jform_searchtype0" name="fields[searchtype]" value="constant" />
								<label for="fields_searchtype0"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_CONSTANT'); ?></label>
							</li>
							<li>
								<input type="radio" id="jform_searchtype1" name="fields[searchtype]" value="value" checked="checked" />
								<label for="fields_searchtype1"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_SEARCHTYPE_TEXT'); ?></label>
							</li>
						</ul>
					</fieldset>
				</div>
				<div class="input-wrap">
					<input type="text" name="fields[searchstring]" id="fields_searchstring" size="50" value="" />
				</div>
				<p>
					<button type="submit" id="searchstrings">
						<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON'); ?>
					</button>
				</p>
			</fieldset>

			<fieldset id="results-container" class="adminform">
				<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND'); ?></span></legend>

				<span id="more-results">
					<a href="javascript:Hubzero.overrider.searchStrings(Hubzero.overrider.states.more);">
						<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_MORE_RESULTS'); ?>
					</a>
				</span>
			</fieldset>

			<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
			<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
			<input type="hidden" name="task" value="" />
			<input type="hidden" name="id" value="<?php echo $this->item->key; ?>" />
			<?php echo Html::input('token'); ?>
		</div>
	</div>
</form>
