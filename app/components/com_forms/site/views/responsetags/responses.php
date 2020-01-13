<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('responseTagsResponses');

$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$hiddenFields = ['form_id' => $formId];
$tagResponsesUrl = $this->tagResponsesUrl;
$responses = $this->responses;
$responseIds = $this->responseIds;
$sortingAction = $this->sortingAction;
$sortingCriteria = $this->sortingCriteria;
$tagString = $this->tagString;

foreach ($responseIds as $id):
	$hiddenFields["response_ids[$id]"] = $id;
endforeach;

$viewHeader = Lang::txt('COM_FORMS_HEADINGS_TAG_RESPONSES_FORM', $formName);

$breadcrumbs = [
	 $formName => ['formsDisplayUrl', [$formId]],
	'Admin' => ['formsEditUrl', [$formId]],
	'Tag Responses' => ['responsesTagsUrl', [$formId, $responseIds]]
];

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "Tag Responses - $formName")
	->display();
?>

<section class="main section">
	<div class="grid">

		<h2>
			<?php echo $viewHeader; ?>
		</h2>

		<div class="col span12 omega">
			<?php
				$this->view('_tagging_form', 'shared')
					->set('action', $tagResponsesUrl)
					->set('hiddenFields', $hiddenFields)
					->set('tagString', $tagString)
					->display();
			?>
		</div>

		<div class="col span10 omega">
			<?php
				$this->view('_response_list', 'shared')
					->set('formId', $formId)
					->set('responses', $responses->rows())
					->set('selectable', false)
					->set('sortingAction', $sortingAction)
					->set('sortingCriteria', $sortingCriteria)
					->display();
			?>
		</div>

	</div>
</section>
