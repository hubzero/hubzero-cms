<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$statusTitle = Lang::txt('COM_FORMS_HEADINGS_STATUS');
$form = $this->form;
$isClosed = $form->isClosed();
$isOpen = $form->isOpen();
$response = $this->response;

if ($acceptedDate = $response->get('accepted')):
	$formattedDate = date('F jS, Y', strtotime($acceptedDate));
	$statusMessage = Lang::txt('COM_FORMS_RESPONSE_STATUS_ACCEPTED', $formattedDate);
elseif ($submissionDate = $response->get('submitted')):
	$formattedDate = date('F jS, Y', strtotime($submissionDate));
	$statusMessage = Lang::txt('COM_FORMS_RESPONSE_STATUS_SUBMITTED', $formattedDate);
elseif ($isClosed):
	$statusMessage = Lang::txt('COM_FORMS_RESPONSE_STATUS_REVIEW');
elseif ($form->get('disabled')):
	$statusMessage = Lang::txt('COM_FORMS_NOTICES_FORM_RESPONSE_DISABLED');
elseif (!$isOpen):
	$statusMessage = Lang::txt('COM_FORMS_NOTICES_FORM_RESPONSE_WAITING');
elseif ($response->isNew()):
	$statusMessage = Lang::txt('COM_FORMS_RESPONSE_STATUS_START');
else:
	$completionPercentage = $response->requiredCompletionPercentage();
	$statusMessage = Lang::txt('COM_FORMS_RESPONSE_STATUS_PERCENT', $completionPercentage);
endif;
?>

<div>
	<h3>
		<?php echo $statusTitle; ?>
	</h3>

	<?php echo $statusMessage; ?>
</div>

