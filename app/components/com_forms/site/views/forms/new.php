<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formForm');

$form = $this->form;
$formAction = $this->formAction;
$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_CREATE_FORM');

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', ['New' => ['formsNewUrl']])
	->set('page', 'New Form')
	->display();
?>

<section class="main section">
	<div class="grid">

		<?php
			$this->view('_form_form')
				->set('action', $formAction)
				->set('form', $form)
				->set('submitValue', $submitValue)
				->display();
		?>

	</div>
</section>
