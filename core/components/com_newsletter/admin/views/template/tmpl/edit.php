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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': ' . $text, 'template.png');

//add toolbar buttons
Toolbar::help('index.php?option=com_help&component=com_newsletter&page=template', true);
Toolbar::spacer();
Toolbar::save();
Toolbar::cancel();
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': '. $text; ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_NAME'); ?>:</label><br />
					<input type="text" name="template[name]" id="field-name" value="<?php echo $this->escape($this->template->name); ?>" />
				</div>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-primary_title_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TITLE_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="template[primary_title_color]" id="field-primary_title_color" value="<?php echo $this->escape($this->template->primary_title_color); ?>" />
					</div>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-primary_text_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TEXT_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="template[primary_text_color]" id="field-primary_text_color" value="<?php echo $this->escape($this->template->primary_text_color); ?>" />
					</div>
				</fieldset>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-secondary_title_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TITLE_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="template[secondary_title_color]" id="field-secondary_title_color" value="<?php echo $this->escape($this->template->secondary_title_color); ?>" />
					</div>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-secondary_text_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TEXT_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="template[secondary_text_color]" id="field-secondary_text_color" value="<?php echo $this->escape($this->template->secondary_text_color); ?>" />
					</div>
				</fieldset>

				<div class="input-wrap">
					<label for="field-template"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TEMPLATE') ?>:</label><br />
					<textarea name="template[template]" id="field-template" cols="100" rows="30"><?php echo $this->escape( $this->template->template ); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span4">
			<?php if ($this->config->get('template_tips')) : ?>
				<span class="hint">
					<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS') ?><br />
					<a target="_blank" href="<?php echo $this->config->get('template_tips'); ?>"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS_HINT'); ?></a>
				</span>
				<br /><br />
			<?php endif; ?>
			<?php if ($this->config->get('template_templates')) : ?>
				<span class="hint">
					<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES'); ?><br />
					<a target="_blank" href="<?php echo $this->config->get('template_templates'); ?>"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES_HINT'); ?></a>
				</span>
				<br /><br />
			<?php endif; ?>
			<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS'); ?></span><br />
			<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS_HINT'); ?>
		</div>
	</div>

	<input type="hidden" name="template[id]" value="<?php echo $this->template->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>