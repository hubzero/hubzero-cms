<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$classes = 'btn btn-success';
$formId = $this->formId;
$isLastPage = $this->isLastPage;
$pagePosition = $this->pagePosition;
$userShouldNotEditResponse = $this->formDisabled;

if ($isLastPage)
{
	$urlFunction = 'formResponseReviewUrl';
	$urlFunctionArgs = [$formId];
	$textKey = 'COM_FORMS_FIELDS_VALUES_REVIEW_RESPONSES';
}
else
{
	$urlFunction = 'formsPageResponseUrl';
	$urlFunctionArgs = [[
		'form_id' => $formId,
		'ordinal' => ($pagePosition + 1)
	]];
	$textKey = 'COM_FORMS_FIELDS_VALUES_NEXT_PAGE';
}
?>

<?php if ($userShouldNotEditResponse):	?>
	<span class="button-container">

		<?php
			$this->view('_link_lang', 'shared')
				->set('classes', $classes)
				->set('urlFunction', $urlFunction)
				->set('urlFunctionArgs', $urlFunctionArgs)
				->set('textKey', $textKey)
				->display();
		?>

	</span>
<?php endif; ?>
