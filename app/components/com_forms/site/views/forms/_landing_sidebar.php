<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$query = $this->query;
$searchFormAction = $this->searchFormAction;
?>

<div class="landing-sidebar">
	<div class="row">
		<?php
			$this->view('_form_search_form')
				->set('query', $query)
				->set('action', $searchFormAction)
				->display();
		?>
	</div>

	<div class="row">
		<?php
			$this->view('_clear_filters_form')
				->set('action', $searchFormAction)
				->display();
		?>
	</div>

	<div class="row link-container">
		<?php
			$this->view('_form_create_link')
				->display();
		?>
	</div>

	<div class="row link-container">
		<?php
			$this->view('_link_lang', 'shared')
				->set('textKey', 'COM_FORMS_LINKS_MY_RESPONSES')
				->set('urlFunction', 'usersResponsesUrl')
				->set('urlFunctionArgs', [])
				->display();
		?>
	</div>

</div>
