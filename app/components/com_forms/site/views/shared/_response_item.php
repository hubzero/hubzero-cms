<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$checkboxName = $this->checkboxName;
$response = $this->response;
$reviewer = $response->getReviewer();
$reviewerId = $reviewer->get('id');
$reviewerName = $reviewer->get('name');
$responseAccepted = $response->get('accepted');
$responseCreated = $response->get('created');
$responseId = $response->get('id');
$responseModified = $response->get('modified');
$responseProgress = $response->requiredCompletionPercentage();
$responseSubmitted = $response->get('submitted');
$selectable = $this->selectable;
$user = $response->getUser();
$userId = $user->get('id');
$usersName = $user->get('name');
?>

<tr class="response-item">

	<?php	if ($selectable): ?>
		<td>
			<input type="checkbox" name="response_ids[]" value="<?php echo $responseId; ?>">
		</td>
	<?php	endif; ?>

	<td>
		<?php
			$this->view('_link', 'shared')
				->set('content', $responseId)
				->set('urlFunction', 'responseFeedUrl')
				->set('urlFunctionArgs', [$responseId])
				->display();
		?>
	</td>

	<td>
		<?php
			$this->view('_link', 'shared')
				->set('content', $usersName)
				->set('urlFunction', 'userProfileUrl')
				->set('urlFunctionArgs', [$userId])
				->display();
		?>
	</td>

	<td>
		<?php echo "$responseProgress%"; ?>
	</td>

	<td>
		<?php
			$this->view('_date', 'shared')
				->set('date', $responseCreated)
				->display();
		?>
	</td>

	<td>
		<?php
			$this->view('_date', 'shared')
				->set('date', $responseModified)
				->display();
		?>
	</td>

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
