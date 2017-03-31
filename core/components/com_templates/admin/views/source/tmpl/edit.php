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

// No direct access.
defined('_HZEXEC_') or die();

Request::setVar('hidemainmenu', true);

$canDo = Components\Templates\Helpers\Utilities::getActions();

Toolbar::title(Lang::txt('COM_TEMPLATES_MANAGER_EDIT_FILE'), 'thememanager');

// Can save the item.
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
}

Toolbar::cancel();
Toolbar::divider();
Toolbar::help('source');

Html::behavior('tooltip');
Html::behavior('formvalidation');
Html::behavior('keepalive');

$editor = Hubzero\Html\Editor::getInstance();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if (task == 'source.cancel' || document.formvalidator.isValid($('#item-form'))) {
			<?php echo $editor->save('field-source'); ?>
			Joomla.submitform(task, document.getElementById('item-form'));
		} else {
			alert('<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>');
		}
	}
</script>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_TEMPLATES_TEMPLATE_FILENAME', $this->file->get('name'), $this->file->template()->element); ?></legend>

		<div class="input-wrap">
			<label for="field-source"><?php echo Lang::txt('COM_TEMPLATES_FIELD_SOURCE_LABEL'); ?></label>
			<div class="editor-border">
				<textarea name="fields[source]" id="field-source" rows="40" cols="80"><?php echo $this->escape($this->file->source()); ?></textarea>
			</div>
		</div>

		<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
		<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
		<input type="hidden" name="task" value="" />
		<?php echo Html::input('token'); ?>
	</fieldset>

	<input type="hidden" name="fields[extension_id]" id="field-extension_id" value="<?php echo $this->escape($this->file->get('extension_id')); ?>" />
	<input type="hidden" name="fields[filename]" id="field-filename" value="<?php echo $this->escape($this->file->get('name')); ?>" />
</form>
