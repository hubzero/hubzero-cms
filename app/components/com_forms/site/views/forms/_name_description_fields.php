<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
$formName = $form->get('name');
$formDescription = $form->get('description');

$fieldsetLegend = Lang::txt('COM_FORMS_FIELDSET_NAME_DESCRIPTION');
$descriptionLabel = Lang::txt('COM_FORMS_FIELDS_DESCRIPTION');
$nameLabel = Lang::txt('COM_FORMS_FIELDS_NAME');
?>

<fieldset>

	<legend>
		<?php echo $fieldsetLegend; ?>
	</legend>

	<div class="grid">
		<div class="col span12">
			<label>
				<?php echo $nameLabel; ?>
				<input name="form[name]" type="text" value="<?php echo $formName; ?>">
			</label>
		</div>

		<div class="col span12">
			<label>
				<?php echo $descriptionLabel; ?>
				<textarea name="form[description]"><?php echo $formDescription; ?></textarea>
			</label>
		</div>
	</div>

</fieldset>
