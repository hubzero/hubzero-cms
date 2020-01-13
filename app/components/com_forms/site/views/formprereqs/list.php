<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

Html::behavior('core');

$this->css('formPrereqsList');
$this->js('formPrereqsList')
	->js('formPrerequisite')
	->js('notify')
	->js('api');

$form = $this->form;
$formAction = $this->formAction;
$formId = $form->get('id');
$formName = $form->get('name');
$forms = $this->forms;
$prereqs = $this->prereqs;
$stepsUpdateText = Lang::txt('COM_FORMS_FIELDS_VALUES_UPDATE_STEPS');

$breadcrumbs = [
	 $formName => ['formsDisplayUrl', [$formId]],
	'Edit' => ['formsEditUrl', [$formId]],
	'Steps' => ['formsPrereqsUrl', [$formId]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', "Form's Pages")
	->display();
?>

<section class="main section">

	<div class="row">
		<?php
			$this->view('_form_edit_nav', 'shared')
				->set('current', 'Steps')
				->set('formId', $formId)
				->display();
		?>
	</div>

	<form action="<?php echo $formAction; ?>">
		<input type="hidden" name="form_id" value="<?php echo $formId; ?>">

		<div class="row prereqs-list-area">
			<?php
				$this->view('_prereqs_list_area')
					->set('forms', $forms)
					->set('prereqs', $prereqs)
					->display();
			?>
		</div>

		<div class="row link-row">
			<span class="steps-update-button">
				<?php if ($prereqs->count() > 0): ?>
					<input class="btn" type="submit" value="<?php echo $stepsUpdateText; ?>">
				<?php endif; ?>
			</span>

			<span>
				<?php
					$this->view('_prereq_new_link')
						->set('formId', $formId)
						->display();
				?>
			</span>
		</div>

	</form>

</section>
