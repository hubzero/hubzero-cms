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
$pageElements = $this->pageElements;
$responseSubmitUrl = $this->responseSubmitUrl;
$reviewText = Lang::txt('COM_FORMS_HEADINGS_REVIEW');
$submitText = Lang::txt('COM_FORMS_FIELDS_VALUES_SUBMIT_FORM_RESPONSE');

$breadcrumbs = [
	$formName => ['formsDisplayUrl', [$formId]],
	$reviewText => ['formsDisplayUrl', [$formId]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "$formName: $reviewText")
	->display();
?>

<section class="main section">

	<div>
		<?php
			$this->view('_form', 'shared')
				->set('action', '')
				->set('disabled', true)
				->set('elements', $pageElements)
				->set('title', $formName)
				->set('title', $formName)
				->display();
		?>
	</div>

	<?php if (!$formDisabled): ?>
		<div class="button-container">
			<form action="<?php echo $responseSubmitUrl; ?>">
				<input type="submit"
					value="<?php echo $submitText; ?>"
					class="btn btn-success">
					<input type="hidden" name="form_id" value="<?php echo $formId; ?>">
			</form>
		</div>
	<?php endif; ?>

</section>
