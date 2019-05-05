<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$canDo = \Components\Citations\Helpers\Permissions::getActions('sponsor');

$text = ($this->task == 'edit' ? Lang::txt('EDIT') : Lang::txt('NEW'));

Toolbar::title(Lang::txt('CITATIONS') . ' ' . Lang::txt('CITATION_SPONSORS') . ': ' . $text, 'citation');
if ($canDo->get('core.edit'))
{
	Toolbar::save();
}
Toolbar::cancel();
Toolbar::spacer();
Toolbar::help('sponsor');

Html::behavior('formvalidation');
Html::behavior('keepalive');

$this->js();
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form" class="form-validate" data-invalid-msg="<?php echo $this->escape(Lang::txt('JGLOBAL_VALIDATION_FORM_FAILED'));?>">
	<fieldset class="adminform">
		<legend><span><?php echo Lang::txt('CITATION_SPONSORS'); ?></span></legend>

		<div class="input-wrap">
			<label for="field-sponsor"><?php echo Lang::txt('CITATION_SPONSORS_NAME'); ?></label>
			<input type="text" name="sponsor[sponsor]" id="field-sponsor" value="<?php echo $this->escape(stripslashes($this->sponsor->get('sponsor'))); ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-link"><?php echo Lang::txt('CITATION_SPONSORS_LINK'); ?></label>
			<input type="text" name="sponsor[link]" id="field-link" value="<?php echo $this->escape(stripslashes($this->sponsor->get('link'))); ?>" />
		</div>
		<div class="input-wrap">
			<label for="field-image"><?php echo Lang::txt('CITATION_SPONSORS_IMAGE'); ?></label>
			<input type="text" name="sponsor[image]" id="field-image" value="<?php echo $this->escape(stripslashes($this->sponsor->get('image'))); ?>" />
		</div>
	</fieldset>

	<input type="hidden" name="sponsor[id]" value="<?php echo $this->sponsor->get('id'); ?>" />
	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="save" />

	<?php echo Html::input('token'); ?>
</form>
