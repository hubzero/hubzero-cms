<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$pages = $form->getPages()
	->order('order', 'asc')
	->rows();
$response = $this->response;
$responseId = $response->get('id');
$user = $response->getUser();
$userId = $user->get('id');
$userIsAdmin = $this->userIsAdmin;
$userName = $user->get('name');
$respondentText = Lang::txt('COM_FORMS_HEADINGS_RESPONDENT', $userName);

$breadcrumbs = [];

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
		$formName => ['formsDisplayUrl', [$formId]]
	];
}

$breadcrumbs['Pages'] = ['usersFormPagesUrl', [$formId, $userId]];

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "$formName Pages: $userName")
	->display();
?>

<section class="main section">
	<div class="grid">

		<nav class="col span12 nav omega">
			<?php
				$this->view('_response_details_nav', 'shared')
					->set('current', 'Pages')
					->set('formId', $formId)
					->set('responseId', $responseId)
					->set('userId', $userId)
					->set('userIsAdmin', $userIsAdmin)
					->display();
			?>
		</nav>

		<?php if ($userIsAdmin): ?>
				<div>
					<h2>
						<?php echo $respondentText; ?>
					</h2>
				</div>
		<?php endif; ?>

		<div>
			<?php
				$this->view('_users_pages_progress')
					->set('pages', $pages)
					->set('response', $response)
					->set('userId', $userId)
					->display();
			?>
		</div>

	</div>
</section>
