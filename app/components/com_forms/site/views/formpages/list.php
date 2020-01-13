<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formPagesList');
$this->js('formPagesList')
	->js('formPage')
	->js('notify')
	->js('api');

Html::behavior('core');

$form = $this->form;
$formId = $form->get('id');
$formName = $form->get('name');
$pages = $this->pages;
$pagesUpdateText = Lang::txt('COM_FORMS_FIELDS_VALUES_UPDATE_PAGES');
$updateAction = $this->updateAction;

$breadcrumbs = [
	 $formName => ['formsDisplayUrl', [$formId]],
	'Edit' => ['formsEditUrl', [$formId]],
	'Pages' => ['formsPagesUrl', [$formId]]
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
				->set('current', 'Pages')
				->set('formId', $formId)
				->display();
		?>
	</div>

	<form action="<?php echo $updateAction; ?>">
		<input type="hidden" name="form_id" value="<?php echo $formId; ?>">

		<div class="row pages-list-area">
			<?php
				$this->view('_pages_list_area')
					->set('pages', $pages)
					->display();
			?>
		</div>

		<div class="row link-row">
			<span class="pages-update-button">
				<?php if ($pages->count() > 0): ?>
					<input class="btn" type="submit" value="<?php echo $pagesUpdateText; ?>">
				<?php endif; ?>
			</span>

			<span>
				<?php
					$this->view('_page_create_link')
						->set('formId', $formId)
						->display();
				?>
			</span>
		</div>
	</form>
</section>
