<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$form = $this->form;
$submitValue = $this->submitValue;
?>

<form id="hubForm" class="full" method="post" action="<?php echo $action; ?>">

	<?php
		$this->view('_name_description_fields')
			->set('form', $form)
			->display();

		$this->view('_dates_option_fields')
			->set('form', $form)
			->display();
	?>

	<div class="button-container">
		<input type="submit" class="btn btn-success" value="<?php echo $submitValue; ?>">
	</div>

</form>
