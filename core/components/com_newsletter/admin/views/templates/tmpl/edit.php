<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = Components\Newsletter\Helpers\Permissions::getActions('newsletter');

//set title
$text = ($this->task == 'edit' ? Lang::txt('COM_NEWSLETTER_EDIT') : Lang::txt('COM_NEWSLETTER_NEW'));
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': ' . $text, 'template.png');
if ($canDo->get('core.edit'))
{
	Toolbar::apply();
	Toolbar::save();
	Toolbar::spacer();
}
Toolbar::cancel();
Toolbar::help('index.php?option=com_help&component=com_newsletter&page=template', true);
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<div class="grid">
		<div class="col span8">
			<fieldset class="adminform">
				<legend><span><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_TEMPLATES') . ': '. $text; ?></span></legend>

				<div class="input-wrap">
					<label for="field-name"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_NAME'); ?>:</label><br />
					<input type="text" name="fields[name]" id="field-name" value="<?php echo $this->escape($this->template->name); ?>" />
				</div>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-primary_title_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TITLE_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="fields[primary_title_color]" id="field-primary_title_color" value="<?php echo $this->escape($this->template->primary_title_color); ?>" />
					</div>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-primary_text_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PRIMARY_TEXT_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="fields[primary_text_color]" id="field-primary_text_color" value="<?php echo $this->escape($this->template->primary_text_color); ?>" />
					</div>
				</fieldset>

				<fieldset class="adminform">
					<legend><span><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY'); ?></span></legend>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-secondary_title_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TITLE_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="fields[secondary_title_color]" id="field-secondary_title_color" value="<?php echo $this->escape($this->template->secondary_title_color); ?>" />
					</div>

					<div class="input-wrap" data-hint="<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?>">
						<label for="field-secondary_text_color"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_SECONDARY_TEXT_COLOR'); ?>:</label><br />
						<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_COLOR_HINT'); ?></span>
						<input type="text" name="fields[secondary_text_color]" id="field-secondary_text_color" value="<?php echo $this->escape($this->template->secondary_text_color); ?>" />
					</div>
				</fieldset>

				<div class="input-wrap">
					<label for="field-template"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TEMPLATE') ?>:</label><br />
					<textarea name="fields[template]" id="field-template" cols="100" rows="30"><?php echo $this->escape( $this->template->template ); ?></textarea>
				</div>
			</fieldset>
		</div>
		<div class="col span4">
			<?php if ($this->config->get('template_tips')) : ?>
				<span class="hint">
					<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS') ?><br />
					<a href="<?php echo $this->config->get('template_tips'); ?>"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_TIPS_HINT'); ?></a>
				</span>
				<br /><br />
			<?php endif; ?>
			<?php if ($this->config->get('template_templates')) : ?>
				<span class="hint">
					<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES'); ?><br />
					<a href="<?php echo $this->config->get('template_templates'); ?>"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_EXAMPLES_HINT'); ?></a>
				</span>
				<br /><br />
			<?php endif; ?>
			<span class="hint"><?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS'); ?></span><br />
			<?php echo Lang::txt('COM_NEWSLETTER_TEMPLATE_PLACEHOLDERS_HINT'); ?>
		</div>
	</div>

	<input type="hidden" name="fields[id]" value="<?php echo $this->template->id; ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>