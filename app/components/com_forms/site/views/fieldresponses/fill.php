<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('pageFill');

$form = $this->form;
$formDisabled = $form->isDisabledFor(User::get('id'));
$formId = $form->get('id');
$formName = $form->get('name');
$page = $this->page;
$pageElements = $this->pageElements;
$pageId = $page->get('id');
$pageIsLast = $form->isLastPage($page);
$pageMetadata = (object) ['name' => 'page_id', 'value' => $pageId];
$pagePosition = $page->ordinalPosition();
$pageTitle = $page->get('title');
$responsesCreateUrl = $this->responsesCreateUrl;
$submitClasses = 'btn btn-success';
$userId =  $this->userId;

if ($pageIsLast)
{
	$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_SAVE_AND_REVIEW');
}
else
{
	$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_SAVE_AND_CONTINUE');
}

$breadcrumbs = [
	$formName => ['formsDisplayUrl', [$formId]],
	'Pages' => ['usersFormPagesUrl', [$formId, $userId]],
	$pageTitle => ['formsPageResponseUrl', [['page_id' => $pageId]]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "$formName: $pageTitle")
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="row">
			<div class="col span12 omega">
				<?php
					$this->view('_form', 'shared')
						->set('action', $responsesCreateUrl)
						->set('disabled', $formDisabled)
						->set('elements', $pageElements)
						->set('hiddenMetadata', [$pageMetadata])
						->set('submitClasses', $submitClasses)
						->set('submitValue', $submitValue)
						->set('title', $pageTitle)
						->display();
				?>
			</div>
		</div>

		<div class="row">
			<div class="col span12 omega">
				<?php
					$this->view('_next_button')
						->set('formDisabled', $formDisabled)
						->set('formId', $formId)
						->set('isLastPage', $pageIsLast)
						->set('pagePosition', $pagePosition)
						->display();
				?>
			</div>
		</div>


		<div class="row">
			<div class="col span12 omega pagination-container">
				<?php
					$this->view('_form_pages_pagination', 'shared')
						->set('formId', $formId)
						->set('pages', $form->getPages())
						->set('position', $pagePosition)
						->display();
				?>
			</div>
		</div>

	</div>
</section>
