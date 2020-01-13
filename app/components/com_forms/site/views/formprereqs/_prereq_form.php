<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$action = $this->action;
$fieldsetLegend = Lang::txt('COM_FORMS_HEADINGS_STEP_INFO');
$formId = $this->formId;
$forms = $this->forms;
$prereq = $this->prereq;
$prereqOrder = $prereq->get('order');
$scopeId = $prereq->get('prerequisite_id');
$selectName = $this->selectName;

$submitValue = $this->submitValue;
$orderLabel = Lang::txt('COM_FORMS_FIELDS_ORDER');
$formLabel = Lang::txt('COM_FORMS_FIELDS_FORM');
?>

<form id="hubForm" class="full" method="post" action="<?php echo $action; ?>">

	<fieldset>
		<legend>
			<?php echo $fieldsetLegend; ?>
		</legend>

		<div class="grid">
			<div class="col span1">
				<label>
					<?php echo $orderLabel; ?>
					<input name="prereq[order]" type="number" min="1" value="<?php echo $prereqOrder; ?>">
				</label>
			</div>

			<div class="col span11 omega">
				<label>
					<?php
						echo $formLabel;
						$this->view('_form_select')
							->set('forms', $forms)
							->set('name', $selectName)
							->set('scopeId', $scopeId)
							->display();
					?>
				</label>
			</div>
		</div>
	</fieldset>

	<div class="row button-container">
		<input type="hidden" name="prereq[form_id]" value="<?php echo $formId; ?>">
		<input type="hidden" name="prereq[prerequisite_scope]" value="forms_forms">
		<input type="submit" class="btn btn-success" value="<?php echo $submitValue; ?>">
	</div>

</form>

