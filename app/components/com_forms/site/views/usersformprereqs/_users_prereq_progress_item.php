<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$prereq = $this->prereq;
$prereqOrder = $prereq->get('order');
$formId = $prereq->getParent('id');
$formName = $prereq->getParent('name');
$userId = $this->userId;
$userIsAdmin = $this->userIsAdmin;
$response = $prereq->getResponse($userId);
$completionStatus = $response->requiredCompletionPercentage();
$responseSubmitted = $response->get('submitted');
$responseAccepted = $response->get('accepted');
$reviewer = $response->getReviewer();
$reviewerId = $reviewer->get('id');
$reviewerName = $reviewer->get('name');

if ($userIsAdmin):
	$formUrl = 'formsEditUrl';
else:
	$formUrl = 'formsDisplayUrl';
endif;

?>

<tr>
	<td><?php echo $prereqOrder; ?></td>

	<td>
		<?php
			$this->view('_link', 'shared')
				->set('content', $formId)
				->set('urlFunction', $formUrl)
				->set('urlFunctionArgs', [$formId])
				->display();
		?>
	</td>

	<td><?php echo $formName; ?></td>

	<td><?php echo "$completionStatus%"; ?></td>

	<td>
		<?php
			$this->view('_date', 'shared')
				->set('date', $responseSubmitted)
				->display();
		?>
	</td>

	<td>
		<?php
			$this->view('_date', 'shared')
				->set('date', $responseAccepted)
				->display();
		?>
	</td>

	<td>
		<?php
			$this->view('_link', 'shared')
				->set('content', $reviewerName)
				->set('urlFunction', 'userProfileUrl')
				->set('urlFunctionArgs', [$reviewerId])
				->display();
		?>
	</td>

</tr>
