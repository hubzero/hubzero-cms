<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('link.css')
	->css('providers.css')
	->js('link.js');

$step = (int) Request::getInt('step', 1);
?>

<header id="content-header">
	<h2><?php echo Lang::txt('Account Setup'); ?></h2>
</header>

<section class="section">
	<div class="prompt-wrap">
		<div class="prompt-container prompt1 <?php echo ($step === 1) ? 'block': 'none'; ?>">
			<div class="prompt">
				<?php echo Lang::txt('Have you ever logged into %s before?', $this->sitename); ?>
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=link&step=2'); ?>">
					<div data-step="1" class="button next forward"><?php echo Lang::txt('JYes'); ?></div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="1" class="button backwards"><?php echo Lang::txt('JNo'); ?></div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt2 <?php echo ($step === 2) ? 'block': 'none'; ?>">
			<div class="prompt">
				<?php echo Lang::txt('Great! Did you want to link your %s account to that existing account or create a new account?', $this->display_name); ?>
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=link&step=3'); ?>">
					<div data-step="2" class="button next link"><?php echo Lang::txt('Link'); ?></div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="2" class="button create-new"><?php echo Lang::txt('Create new'); ?></div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt3 <?php echo ($step === 3) ? 'block': 'none'; ?>">
			<div class="prompt">
				<?php echo Lang::txt('We can do that. Just login with that existing account now and we\'ll link them up!'); ?>
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=logout&return=' .
					base64_encode(
						Route::url('index.php?option=' . $this->option . '&reset=1&return=' .
							base64_encode(
								Route::url('index.php?option=' . $this->option . '&authenticator=' . $this->hzad->authenticator, false)
							),
						false)
					)); ?>">
					<div data-step="3" class="button ok"><?php echo Lang::txt('OK'); ?></div>
				</a>
				<a href="<?php echo Route::url('index.php?option=' . $this->option . '&task=link&step=2'); ?>">
					<div data-step="3" class="button previous back"><?php echo Lang::txt('Go back'); ?></div>
				</a>
			</div>
		</div>
	</div>
</section>