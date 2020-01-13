<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->field;
$fieldName = $field->get('name');
$inputValue = $field->getInputValue();
$min = $field->get('min');
$max = $field->get('max');
$step = $field->get('step');
$userInputName = $fieldName . '[response]';
?>

<div class="field-wrap">
	<input type="number" name="<?php echo $userInputName; ?>"
		min="<?php echo $min; ?>"
		max="<?php echo $max; ?>"
		step="<?php echo $step; ?>"
		value="<?php echo $inputValue; ?>">

	<?php
		$this->view('_form_field_metadata_fields')
			->set('field', $field)
			->display();
	?>
</div>
