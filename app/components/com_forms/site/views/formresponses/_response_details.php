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
$response = $this->response;
$responseId = $response->get('id');
$tagString = $this->tagString;
$tagUpdateUrl = $this->tagUpdateUrl;
$userIsAdmin = $this->userIsAdmin;

$hiddenFields = [
	'form_id' => $formId,
	'response_id' => $responseId
];
?>

<div>
	<h1><?php echo $formName; ?></h1>
</div>

<div class="grid">
  <?php if ($userIsAdmin): ?>
    <div class="col span6">
      <?php $this->view('_response_owner')
          ->set('response', $response)
          ->display();
      ?>
    </div>
  <?php endif; ?>

  <div class="col span6 omega">
    <?php
      $this->view('_response_status', 'forms')
        ->set('form', $form)
        ->set('response', $response)
        ->display();
    ?>
  </div>
</div>

<div>
	<?php
		$this->view('_response_dates')
			->set('form', $form)
			->set('started', $response->get('created'))
			->display();
	?>
</div>

<?php if ($userIsAdmin): ?>
  <div>
    <h3>
      <?php echo Lang::txt('COM_FORMS_HEADINGS_TAGS'); ?>
    </h3>

    <?php
      $this->view('_tagging_form', 'shared')
        ->set('action', $tagUpdateUrl)
        ->set('hiddenFields', $hiddenFields)
        ->set('isHubForm', false)
        ->set('tagString', $tagString)
        ->display();
    ?>
  </div>
<?php endif; ?>
