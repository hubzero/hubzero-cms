<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('prereqForm');

$action = $this->action;
$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$forms = $this->forms;
$prereq = $this->prereq;
$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_CREATE_STEP');

$breadcrumbs = [
	$formName => ['formsDisplayUrl', [$formId]],
	'New Step' => ['formsPrereqsNewUrl', [$formId]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', 'New Step')
	->display();
?>

<section class="main section">
	<div class="grid">

		<?php
			$this->view('_prereq_form')
				->set('action', $action)
				->set('formId', $formId)
				->set('forms', $forms)
				->set('prereq', $prereq)
				->set('selectName', 'prereq[prerequisite_id]')
				->set('submitValue', $submitValue)
				->display();
		?>

	</div>
</section>

