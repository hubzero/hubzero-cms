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
$option = $this->option;
$optionId = $option->id;
$option->value = $optionId;
$label = $option->label;
$namePrefix = "$this->name[response]";
$name = $namePrefix . '[selected]';
$idFieldName = $namePrefix . '[id]';
$userResponse = json_decode($field->getInputValue());

if ($userResponse && $userResponse->selected == $optionId)
{
	$isSelected = true;
}
else if (!$userResponse)
{
	$isSelected = isset($option->selected) && $option->selected;
}
else
{
	$isSelected = false;
}

$this->view('_form_field_list_group_item')
	->set('inline', $inline)
	->set('isSelected', $isSelected)
	->set('name', $name)
	->set('option', $option)
	->set('type', 'radio')
	->display();

$this->view('_form_field_metadata_fields')
  ->set('field', $field)
  ->display();

