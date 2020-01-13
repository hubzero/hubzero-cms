<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formSearchForm');
$this->js('searchForm');

$action = $this->action;
$query = $this->query;
$queryArchived = $query->get('archived');
$queryClosingTime = $query->get('closing_time');
$queryClosingTimeRelative = $query->get('closing_time_relative_operator');
$queryDisabled = $query->get('disabled');
$queryLocked = $query->get('responses_locked');
$queryName = $query->getValue('name');
$queryOpeningTime = $query->get('opening_time');
$queryOpeningTimeRelative = $query->get('opening_time_relative_operator');
?>

<form class="search-form" method="post" action="<?php echo $action; ?>">

	<div class="row">
		<span class="header">
			<span class="master-caret fontcon" data-visible="true">
				&#xf0d8;
			</span>
		</span>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_NAME'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_text_field', 'shared')
					->set('fuzzyEnd', '1')
					->set('name', 'name')
					->set('operator', 'like')
					->set('value', $queryName)
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_OPENING_DATE'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_relative_date_fields', 'shared')
					->set('data', $queryOpeningTime)
					->set('name', 'opening_time')
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_CLOSING_DATE'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_relative_date_fields', 'shared')
					->set('data', $queryClosingTime)
					->set('name', 'closing_time')
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_RESPONSES'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_binary_radio_set', 'shared')
					->set('data', $queryLocked)
					->set('falseTextKey', 'COM_FORMS_FIELDS_RESPONSES_EDITABLE')
					->set('name', 'responses_locked')
					->set('operator', '=')
					->set('trueTextKey', 'COM_FORMS_FIELDS_RESPONSES_LOCKED')
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_DISABLED'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_binary_radio_set', 'shared')
					->set('data', $queryDisabled)
					->set('name', 'disabled')
					->set('operator', '=')
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
		<?php
			$this->view('_collapsible_field_header', 'shared')
				->set('title', Lang::txt('COM_FORMS_FIELDS_ARCHIVED'))
				->set('isRequired', false)
				->display();
		?>
		<div class="content">
			<?php
				$this->view('_search_binary_radio_set', 'shared')
					->set('data', $queryArchived)
					->set('name', 'archived')
					->set('operator', '=')
					->display();
			?>
		</div>
		<hr>
	</div>

	<div class="row">
			<?php echo Html::input('token'); ?>
			<input class="btn" type="submit"
				value="<?php echo Lang::txt('COM_FORMS_FIELDS_SEARCH'); ?>">
	</div>

</form>
