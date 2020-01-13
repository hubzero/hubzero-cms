<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->field;
$inline = $this->inline;
$isSelected = false;
$fieldName = htmlspecialchars($this->name, ENT_COMPAT);
$checkboxPrefix = $fieldName . '[response][other]';
$checkboxName = $checkboxPrefix . '[selected]';
$textInputName = $checkboxPrefix . '[text]';
$userResponse = json_decode($field->getInputValue());
$label = 'other';
$option = (object)  [
	'label' => $label,
	'value' => 1
];
$otherText = '';

if ($userResponse && $userResponse->$label && isset($userResponse->$label->selected))
{
	$isSelected = true;
	$otherText =$userResponse->$label->text;
}

$this->view('_form_field_list_group_item')
	->set('inline', $inline)
	->set('isSelected', $isSelected)
	->set('name', $checkboxName)
	->set('option', $option)
	->set('type', 'checkbox')
	->display();
?>

<label>
	<input type="text"
		name="<?php echo $textInputName; ?>"
		value="<?php echo $otherText; ?>"
	/>
</label>
