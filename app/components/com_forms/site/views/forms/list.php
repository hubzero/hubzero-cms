<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('formList');

$formListUrl = $this->formListUrl;
$forms = $this->forms;
$query = $this->query;
$searchFormAction = $this->searchFormAction;

$this->view('_forms_breadcrumbs', 'shared')
	->set('breadcrumbs', [])
	->set('page', 'Forms list')
	->display();
?>

<section class="main section">
	<div class="grid">

		<div class="col span2">
			<?php
				$this->view('_landing_sidebar')
					->set('query', $query)
					->set('searchFormAction', $searchFormAction)
					->display();
			?>
		</div>

		<div class="col span10 omega">
			<?php
				$this->view('_form_list')
					->set('forms', $forms)
					->display();
			?>

			<form method="POST" action="<?php echo $formListUrl; ?>">
				<?php echo $forms->pagination; ?>
			</form>
		</div>

	</div>
</section>
