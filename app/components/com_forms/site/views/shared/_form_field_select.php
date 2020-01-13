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
$multiple = $field->get('multiple') ? 'multiple' : '';
$options = $field->getOptions();
$selectedOptionValue = $field->getInputValue();
$userInputName = $fieldName . '[response]';
?>

<select name="<?php echo $userInputName; ?>" <?php echo $multiple; ?>>

	<?php
		foreach($options as $option):
			$selected = $option->value === $selectedOptionValue;
			$selected = $selected ? $selected : (isset($option->selected) && $option->selected);
			$this->view("_form_field_select_option")
				->set('option', $option)
				->set('selected', $selected)
				->display();
		endforeach;

		$this->view('_form_field_metadata_fields')
			->set('field', $field)
			->display();
	?>

</select>
