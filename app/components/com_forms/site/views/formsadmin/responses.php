<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formsAdminResponses');

$this->js('notify')
	->js('formsAdminResponsesListActions')
	->js('formsAdminResponsesListCheckbox')
	->js('formsAdminResponsesListSorting')
	->js('formsAdminResponsesList');

$responsesEmailUrl = $this->responsesEmailUrl;
$responsesTagsUrl = $this->responsesTagsUrl;
$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$responses = $this->responses;
$responseListUrl = $this->responseListUrl;
$sortingCriteria = $this->sortingCriteria;
$responsesCount = $responses->count();
$breadcrumbs = [
	 $formName => ['formsDisplayUrl', [$formId]],
	'Admin' => ['formsEditUrl', [$formId]],
	'Responses' => ['formsResponseList', [$formId]]
];

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "$formName Responses")
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="col span12 nav omega">
			<?php
				$this->view('_form_edit_nav', 'shared')
					->set('current', 'Responses')
					->set('formId', $formId)
					->display();
			?>
		</div>

		<div class="col span12 omega list-actions">
			<?php if ($responsesCount > 0): ?>
				<span id="email-respondents-button" class="list-action">
					<?php
						$this->view('_email_respondents_form')
							->set('action', $responsesEmailUrl)
							->set('formId', $formId)
							->display();
					?>
				</span>

				<span id="tag-responses-button" class="list-action">
					<?php
						$this->view('_tag_responses_form')
							->set('action', $responsesTagsUrl)
							->set('formId', $formId)
							->display();
					?>
				</span>
			<?php endif; ?>
		</div>

		<div class="col span12 omega">
			<?php
				$this->view('_response_list_area')
					->set('formId', $formId)
					->set('responses', $responses)
					->set('sortingAction', $responseListUrl)
					->set('sortingCriteria', $sortingCriteria)
					->display();

				$this->view('_pagination', 'shared')
					->set('minDisplayLimit', 4)
					->set('pagination', $responses->pagination)
					->set('paginationUrl', $responseListUrl)
					->set('recordsCount', $responsesCount)
					->display();
			?>

			<?php if ($responsesCount > 0): ?>
				<span>
					<?php
						$this->view('_protected_link', 'shared')
							->set('authMethod', 'canCurrentUserEditForm')
							->set('authArgs', [$form])
							->set('textKey', 'COM_FORMS_FIELDS_RESPONSES_EXPORT')
							->set('urlFunction', 'formResponsesExportUrl')
							->set('urlFunctionArgs', [$formId])
							->display();
					?>
				</span>
			<?php endif; ?>
		</div>

	</div>
</section>
