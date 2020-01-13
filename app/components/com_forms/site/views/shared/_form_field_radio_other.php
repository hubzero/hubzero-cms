<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->field;
$fieldValue = json_decode($field->getInputValue());
$inline = $this->inline;
$isSelected = false;
$fieldName = htmlspecialchars($this->name, ENT_COMPAT);
$radioPrefix = $fieldName . '[response]';
$radioName = $radioPrefix . '[selected]';
$textInputName = $radioPrefix . '[text]';
$label = 'other';
$option = (object) [
	'id' => $label,
	'label' => $label,
	'value' => 'other'
];
$otherText = '';

if ($fieldValue && $fieldValue->selected == $option->id)
{
	$isSelected = true;
	$otherText = $fieldValue->text;
}

$this->view('_form_field_list_group_item')
	->set('inline', $inline)
	->set('isSelected', $isSelected)
	->set('name', $radioName)
	->set('option', $option)
	->set('type', 'radio')
	->display();
?>

<label>
	<input type="text"
		name="<?php echo $textInputName; ?>" /
		value="<?php echo $otherText; ?>" />
</label>
