<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formDisplay');

$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$response = $this->response;

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', [$formName => ['formsDisplayUrl', [$formId]]])
	->set('page', $formName)
	->display();
?>

<section class="main section">
	<div class="grid">
		<div class="row">

			<div class="col span7">
				<div>
					<?php
						$this->view('_form_overview')
							->set('form', $form)
							->set('response', $response)
							->display();
					?>
				</div>

				<div class="form-response-link">
					<?php
						$this->view('_form_response_link')
							->set('form', $form)
							->set('response', $response)
							->display();
					?>
				</div>

				<div class="edit-link-container">
					<?php
						$this->view('_form_edit_link')
							->set('form', $form)
							->display();
					?>
				</div>
			</div>

		</div>
	</div>
</section>
