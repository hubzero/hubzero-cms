<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$response = $this->response;
$responseId = $response->get('id');
$isAccepted = !!$response->get('accepted');

if ($isAccepted)
{
	$submitText = Lang::txt('COM_FORMS_FIELDS_VALUES_ACCEPT_REVERSE');
	$submitClasses = 'btn-error';
}
else
{
	$submitText = Lang::txt('COM_FORMS_FIELDS_VALUES_ACCEPT');
	$submitClasses = 'btn-success';
}
?>

<form class="acceptance-form" action="<?php echo $action; ?>">
	<input type="hidden" name="response_id" value="<?php echo $responseId; ?>">
	<input type="hidden" name="accepted" value="<?php echo !$isAccepted; ?>">

	<input type="submit"
		class="btn <?php echo $submitClasses; ?>"
		value="<?php echo $submitText; ?>">
</form>
