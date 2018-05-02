<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	->js('overrider.js');
?>
<script type="text/javascript">
	jQuery(document).ready(function($){
		$('#jform_searchstring').on('focus', function() {
			if (!Joomla.overrider.states.refreshed) {
				<?php if (Request::getVar('cache_expired')): ?>
				Joomla.overrider.refreshCache();
				Joomla.overrider.states.refreshed = true;
				<?php endif; ?>
			}
			$(this).removeClass('invalid');
		});
	});

	Joomla.submitbutton = function(task)
	{
		if (task == 'override.cancel' || document.formvalidator.isValid($('#item-form'))) {
			Joomla.submitform(task, document.getElementById('item-form'));
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller . '&task=edit&id=' . $this->item->key); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<div class="grid">
		<div class="col span7">
			<fieldset class="adminform">
				<legend><span><?php echo empty($this->item->key) ? Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_NEW_OVERRIDE_LEGEND') : Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_EDIT_EDIT_OVERRIDE_LEGEND'); ?></span></legend>

				<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_DESC'); ?>">
					<label for="field-key"><?php echo Lang::txt('COM_LANGUAGES_OVERRIDE_FIELD_KEY_LABEL'); ?>: <span class="required"><?php echo Lang::txt('JOPTION_REQUIRED'); ?></span></label>
					<input type="text" name="fields[key]" id="field-key" size="60" value="<?php echo $this->escape($this->item->key); ?>" />
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
					<button type="submit" onclick="Joomla.overrider.searchStrings();return false;">
						<?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_SEARCH_BUTTON'); ?>
					</button>
				</p>
			</fieldset>

			<fieldset id="results-container" class="adminform">
				<legend><span><?php echo Lang::txt('COM_LANGUAGES_VIEW_OVERRIDE_RESULTS_LEGEND'); ?></span></legend>

				<span id="more-results">
					<a href="javascript:Joomla.overrider.searchStrings(Joomla.overrider.states.more);">
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
