<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('responseFeed');

$comment = $this->comment;
$createCommentUrl = $this->createCommentUrl;
$feedItems = $this->feedItems;
$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$userIsAdmin = $this->userIsAdmin;
$response = $this->response;
$responseId = $response->get('id');
$tagString = $this->tagString;
$tagUpdateUrl = $this->tagUpdateUrl;
$user = $response->getUser();
$userId = $user->get('id');
$userName = $user->get('name');

if ($userIsAdmin)
{
  $breadcrumbs = [
    $formName => ['formsDisplayUrl', [$formId]],
    'Admin' => ['formsEditUrl', [$formId]],
    'Responses' => ['formsResponseList', [$formId]],
    $userName => ['responseFeedUrl', [$responseId]]
  ];
}
else
{
  $breadcrumbs = [
    'Responses' => ['usersResponsesUrl'],
    $formName => ['formsDisplayUrl', [$formId]],

  ];
}

$breadcrumbs['Feed'] = ['responseFeedUrl', [$responseId]];

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "$formName Feed: $userName")
	->display();
?>

<section class="main section">
	<div class="grid">

		<nav class="col span12 nav omega">
			<?php
				$this->view('_response_details_nav', 'shared')
					->set('current', 'Feed')
					->set('formId', $formId)
					->set('responseId', $responseId)
					->set('userId', $userId)
					->set('userIsAdmin', $userIsAdmin)
					->display();
			?>
		</nav>

		<div class="col span6 response-details">
			<?php
				$this->view('_response_details')
					->set('form', $form)
					->set('response', $response)
					->set('tagString', $tagString)
					->set('tagUpdateUrl', $tagUpdateUrl)
					->set('userIsAdmin', $userIsAdmin)
					->display();
			?>
		</div>

		<div class="col span6 response-feed-container omega">
			<?php
				$this->view('_response_feed')
					->set('comment', $comment)
					->set('createCommentUrl', $createCommentUrl)
					->set('feedItems', $feedItems)
					->set('formId', $formId)
					->set('responseId', $responseId)
					->display();
			?>
		</div>

	</div>
</section>
