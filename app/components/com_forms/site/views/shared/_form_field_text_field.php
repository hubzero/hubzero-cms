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
$maxLength = $field->get('max_length');
$size = $maxLength;
$userInputName = $fieldName . '[response]';
?>

<div class="field-wrap">
	<input type="text"
		maxlength="<?php echo $maxLength; ?>"
		name="<?php echo $userInputName; ?>"
		size="<?php echo $size; ?>"
		value="<?php echo $inputValue; ?>">

	<?php
		$this->view('_form_field_metadata_fields')
			->set('field', $field)
			->display();
	?>
</div>
