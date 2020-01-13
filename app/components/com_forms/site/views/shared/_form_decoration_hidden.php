<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$input = $this->decoration;
$fieldName = $input->get('name');
$userInputName = $fieldName . '[response]';
$value = $input->get('default_value');
?>

<div>
	<input type="hidden"
		name="<?php echo $userInputName; ?>"
		value="<?php echo $value; ?>">

	<?php
		$this->view('_form_field_metadata_fields')
			->set('field', $input)
			->display();
	?>
</div>
