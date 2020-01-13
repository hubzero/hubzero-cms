<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$flag = $this->flag;
$name = $this->name;
$trueTextKey = isset($this->trueTextKey) ? $this->trueTextKey : 'COM_FORMS_FIELDS_RESPONSES_YES';
$falseTextKey = isset($this->falseTextKey) ? $this->falseTextKey : 'COM_FORMS_FIELDS_RESPONSES_NO';

$radios = [
	[
		'checked' => !!$flag,
		'name' => $name,
		'text' => Lang::txt($trueTextKey),
		'value' => 1
	],
	[
		'checked' => ($flag != null && !$flag),
		'name' => $name,
		'text' => Lang::txt($falseTextKey),
		'value' => 0
	],
];

$this->view('_inline_radio_list', 'shared')
	->set('radios', $radios)
	->display();
?>
