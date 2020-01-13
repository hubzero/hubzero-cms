<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;
?>

<div class="grid">
	<div class="col span6">
		<?php
			$this->view('_forms_dates_open')
				->set('isOpen', $form->isOpen())
				->set('openingTime', $form->get('opening_time'))
				->display();
		?>
	</div>

	<div class="col span6 omega">
		<?php
			$this->view('_forms_dates_closes')
				->set('isClosed', $form->isClosed())
				->set('closingTime', $form->get('closing_time'))
				->display();
		?>
	</div>
</div>
