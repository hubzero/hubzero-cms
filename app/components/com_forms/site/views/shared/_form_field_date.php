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
$userInputName = $fieldName . '[response]';
?>

<div class="field-wrap">
	<input type="date" name="<?php echo $userInputName; ?>"
		value="<?php echo $inputValue; ?>">

	<?php
		$this->view('_form_field_metadata_fields')
			->set('field', $field)
			->display();
	?>
</div>
