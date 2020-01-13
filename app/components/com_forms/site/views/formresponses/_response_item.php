<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$response = $this->response;
$form = $response->getForm();
$formId = $form->get('id');
$formName = $form->get('name');
$responseId = $response->get('id');
?>

<li class="response-item">
	<span class="grid">

		<span class="col span5">
			<?php
				$this->view('_link', 'shared')
					->set('content', $formName)
					->set('urlFunction', 'responseFeedUrl')
					->set('urlFunctionArgs', [$responseId])
					->display();
			?>
		</span>

		<span class="col span7 omega">
			<?php
				$this->view('_response_status_notice')
					->set('form', $form)
					->set('response', $response)
					->display();
			?>
		</span>

	</span>
</li>
