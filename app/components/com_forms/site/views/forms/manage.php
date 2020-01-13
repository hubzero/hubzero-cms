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
$action = $this->formAction;
$formId = $form->get('id');
$formName = $form->get('name');
$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_UPDATE_FORM');

$breadcrumbs = [
	 $formName => ['formsDisplayUrl', [$formId]],
	'Manage' => ['formsEditUrl', [$formId]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', Lang::txt('COM_FORMS_FIELDS_MANAGE', $formName))
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="row">
			<div class="col span12 omega">
				<?php
					$this->view('_form_edit_nav', 'shared')
						->set('current', 'Form Info')
						->set('formId', $formId)
						->display();
				?>
			</div>
		</div>

		<div class="row">
			<div class="col span12 omega">
				<?php
					$this->view('_form_form')
						->set('action', $action)
						->set('form', $form)
						->set('submitValue', $submitValue)
						->display();
				?>
			</div>
		</div>

	</div>
</section>
