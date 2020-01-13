<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formsAdminResponsesList');

$checkboxesName = 'responses_ids[]';
$columns = [
	'ID' => 'id',
	'User' => 'user_id',
	'Completion Percentage' => 'completion_percentage',
	'Started' => 'created',
	'Last Activity' => 'modified',
	'Submitted' => 'submitted',
	'Accepted' => 'accepted',
	'Reviewed By' => 'reviewed_by'
];
$formId = $this->formId;
$responses = $this->responses;
$responsesSelectable = isset($this->selectable) ? $this->selectable : true;
$sortingAction = $this->sortingAction;
$sortingCriteria = $this->sortingCriteria;
?>

<table id="response-list">
	<thead>
		<tr>

			<?php	if ($responsesSelectable): ?>
				<td>
					<input id="master-checkbox" type="checkbox">
				</td>
			<?php	endif; ?>

			<?php
				$this->view('_sortable_column_headers', 'shared')
					->set('columns', $columns)
					->set('sortingCriteria', $sortingCriteria)
					->display();
			?>
		</tr>
	</thead>

	<tbody>
		<?php
			foreach ($responses as $response):
				$this->view('_response_item')
					->set('checkboxName', $checkboxesName)
					->set('response', $response)
					->set('selectable', $responsesSelectable)
					->display();
			endforeach;
		?>
	</tbody>
</table>

<form id="sort-form" action="<?php echo $sortingAction; ?>">
	<input type="hidden" name="form_id" value="<?php echo $formId; ?>">
	<input type="hidden" name="sort_direction">
	<input type="hidden" name="sort_field">
</form>
