<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('pageEdit')
	->css('pageForm');

$action = $this->action;
$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$page = $this->page;
$pageId = $page->get('id');
$submitValue = Lang::txt('COM_FORMS_FIELDS_VALUES_UPDATE_PAGE');

$breadcrumbs = [
	$formName => ['formsDisplayUrl', [$formId]],
	'Edit' => ['formsEditUrl', [$formId]],
	'Pages' => ['formsPagesUrl', [$formId]],
	$pageId => ['pagesEditUrl', [$pageId]]
];
$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', $breadcrumbs)
	->set('page', 'Edit Page')
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="col span12 omega">
			<?php
				$this->view('_page_form')
					->set('action', $action)
					->set('page', $page)
					->set('submitValue', $submitValue)
					->display();
			?>
		</div>

		<div class="col span12 omega">
			<?php
				$this->view('_fields_form')
					->set('pageId', $pageId)
					->display();
			?>
		</div>

	</div>
</section>
