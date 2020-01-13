<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$fieldsetLegend = Lang::txt('COM_FORMS_FIELDSET_DATES_OPTIONS');

$form = $this->form;
$formArchived = $form->get('archived');
$formClosingTime = $form->get('closing_time');
$formattedClosingTime = $formClosingTime ? (new DateTime($formClosingTime))
	->format('Y-m-d\TH:i:s') : '';
$formDisabled = $form->get('disabled');
$formOpeningTime = $form->get('opening_time');
$formattedOpeningTime = $formOpeningTime ? (new DateTime($formOpeningTime))
	->format('Y-m-d\TH:i:s') : '';
$formResponsesLocked = $form->get('responses_locked');

$archivedLabel = Lang::txt('COM_FORMS_FIELDS_ARCHIVED');
$closingDateLabel = Lang::txt('COM_FORMS_FIELDS_CLOSING_DATE');
$disabledLabel = Lang::txt('COM_FORMS_FIELDS_DISABLED');
$openingDateLabel = Lang::txt('COM_FORMS_FIELDS_OPENING_DATE');
$responsesLabel = Lang::txt('COM_FORMS_FIELDS_RESPONSES');
?>

<fieldset>

	<legend>
		<?php echo $fieldsetLegend; ?>
	</legend>

	<div class="grid">
		<div class="col span5 datetimes-container">
			<label>
				<?php echo $openingDateLabel; ?>
				<div class="datetime-container">
					<input name="form[opening_time]" type="datetime-local"
						value="<?php echo $formattedOpeningTime; ?>">
				</div>
			</label>

			<label>
				<?php echo $closingDateLabel; ?>
				<div class="datetime-container">
					<input name="form[closing_time]" type="datetime-local"
						value="<?php echo $formattedClosingTime; ?>">
				</div>
			</label>
		</div>

		<div class="col span2 offset1">
			<label>
				<?php echo $responsesLabel; ?>
				<div class="radios-container">
					<?php
						$this->view('_binary_inline_radio_list', 'shared')
							->set('falseTextKey', 'COM_FORMS_FIELDS_RESPONSES_EDITABLE')
							->set('flag', $formResponsesLocked)
							->set('name', 'form[responses_locked]')
							->set('trueTextKey', 'COM_FORMS_FIELDS_RESPONSES_LOCKED')
							->display();
					?>
				</div>
			</label>
		</div>

		<div class="col span2">
			<label>
				<?php echo $disabledLabel; ?>
				<div class="radios-container">
					<?php
						$this->view('_binary_inline_radio_list', 'shared')
							->set('flag', $formDisabled)
							->set('name', 'form[disabled]')
							->display();
					?>
				</div>
			</label>
		</div>

		<div class="col span2 omega">
			<label>
				<?php echo $archivedLabel; ?>
				<div class="radios-container">
					<?php
						$this->view('_binary_inline_radio_list', 'shared')
							->set('flag', $formArchived)
							->set('name', 'form[archived]')
							->display();
					?>
				</div>
			</label>
		</div>
	</div>

</fieldset>
