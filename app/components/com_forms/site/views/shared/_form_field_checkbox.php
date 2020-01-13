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
$isSelected = isset($option->selected) && $option->selected;
$optionId = $option->id;
$name = "$this->name[response][$optionId]";
$userResponse = $field->getInputValue();
$decodedResponse = json_decode($userResponse);
$responseObject = $decodedResponse ? $decodedResponse : (object) [];
$selectedOptionsIds = get_object_vars($responseObject);

if ($userResponse && isset($selectedOptionsIds[$optionId]))
{
	$isSelected = true;
}

$this->view('_form_field_list_group_item')
	->set('inline', $inline)
	->set('isSelected', $isSelected)
	->set('name', $name)
	->set('option', $option)
	->set('type', 'checkbox')
	->display();

$this->view('_form_field_metadata_fields')
  ->set('field', $field)
  ->display();
