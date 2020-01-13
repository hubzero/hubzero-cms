<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
$response = $this->response;

if ($accepted = $response->get('accepted')):
	$args = [date('F jS, Y', strtotime($accepted))];
	$key = 'COM_FORMS_RESPONSE_STATUS_ACCEPTED';
elseif ($form->isOpen() && $submitted = $response->get('submitted')):
	$args = [date('F jS, Y', strtotime($submitted))];
	$key = 'COM_FORMS_RESPONSE_STATUS_SUBMITTED';
elseif ($form->isClosed()):
	$args = [round($form->getDaysSinceClose())];
	$key = 'COM_FORMS_NOTICES_FORM_CLOSED';
elseif ($form->isOpen()):
	$args = [round($form->getDaysUntilClose())];
	$key = 'COM_FORMS_NOTICES_FORM_CLOSES_IN';
else:
	$args = [round($form->getDaysUntilOpen())];
	$key = 'COM_FORMS_NOTICES_FORM_OPENS_IN';
endif;

$responseStatus = Lang::txt($key, ...$args);
?>

<span>
	<?php echo $responseStatus; ?>
</span>
