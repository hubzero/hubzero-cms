<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$field = $this->element;
$type = $this->type;

$fieldTypeMap = [
	'checkbox-group' => '_form_field_checkbox_group',
	'date' => '_form_field_date',
	'number' => '_form_field_number',
	'radio-group' => '_form_field_radio_group',
	'select' => '_form_field_select',
	'text' => '_form_field_text_field',
	'textarea' => '_form_field_text_area'
];
$partialName = $fieldTypeMap[$type];
?>

<fieldset>
	<?php
		$this->view('_form_field_label')
			->set('field', $field)
			->display();

		$this->view($partialName)
			->set('field', $field)
			->display();
	?>
</fieldset>
