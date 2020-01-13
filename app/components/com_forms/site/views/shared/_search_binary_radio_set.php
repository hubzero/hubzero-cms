<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$name = $this->name;

$data = $this->data;
$falseTextKey = isset($this->falseTextKey) ? $this->falseTextKey : 'COM_FORMS_FIELDS_RESPONSES_NO';
$operator = $data->getOperator();
$trueTextKey = isset($this->trueTextKey) ? $this->trueTextKey : 'COM_FORMS_FIELDS_RESPONSES_YES';
$value = $data->getValue();
?>

<input type="hidden" name="query[<?php echo $name; ?>][value]"
	value="">

<?php
	$this->view('_binary_inline_radio_list', 'shared')
		->set('falseTextKey', $falseTextKey)
		->set('flag', $value)
		->set('name', "query[$name][value]")
		->set('trueTextKey', $trueTextKey)
		->display();
?>

<input type="hidden" name="query[<?php echo $name; ?>][operator]"
	value="=">
