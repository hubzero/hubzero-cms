<?php
/*
 * @package   hubzero-cms
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$form = $this->form;

$name = $form->get('name');
$closingTime = $form->get('closing_time');
$displayUrl = $this->displayUrl;
$formId = $form->get('id');
$disabled = $form->get('disabled');
$openingTime = $form->get('opening_time');
?>

<li class="form-item">
	<a href="<?php echo $displayUrl; ?>">
		<span class="grid">

			<span class="col span4"><?php echo $name; ?></span>

			<span class="col span3">
				<?php
					$this->view('_date', 'shared')
						->set('date', $openingTime)
						->display();
				?>
			</span>

			<span class="col span3">
				<?php
					$this->view('_date', 'shared')
						->set('date', $closingTime)
						->display();
				?>
			</span>

		</span>
	</a>
</li>
