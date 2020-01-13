<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$forms = $this->forms;
$name = $this->name;
$nullOptionText = Lang::txt('COM_FORMS_FIELDS_VALUES_SELECT_FORM');
$scopeId = $this->scopeId;
?>

<select name="<?php echo $name; ?>">

	<option selected disabled hidden>
		<?php echo $nullOptionText; ?>
	</option>

	<?php
		foreach($forms as $form):
			$formId = $form->get('id');
	?>
		<option value="<?php echo $formId; ?>"
			<?php if ($formId == $scopeId) echo 'selected'; ?>>
			<?php echo $form->get('name'); ?>
		</option>
	<?php endforeach; ?>

</select>
