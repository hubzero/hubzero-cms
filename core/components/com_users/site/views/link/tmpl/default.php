<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$step = (int) Request::getInt('step', 1);
?>

<header id="content-header">
	<h2>Account Setup</h2>
</header>

<section class="section">
	<div class="prompt-wrap">
		<div class="prompt-container prompt1 <?php echo ($step === 1) ? 'block': 'none'; ?>">
			<div class="prompt">
				Have you ever logged into <?php echo $this->sitename; ?> before?
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=2'); ?>">
					<div data-step="1" class="button next forward">Yes</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="1" class="button backwards">No</div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt2 <?php echo ($step === 2) ? 'block': 'none'; ?>">
			<div class="prompt">
				Great! Did you want to link your <?php echo $this->display_name; ?> account to that existing account or create a new account?
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=3'); ?>">
					<div data-step="2" class="button next link">Link</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_members&controller=register&task=update'); ?>">
					<div data-step="2" class="button create-new">Create new</div>
				</a>
			</div>
		</div>

		<div class="prompt-container prompt3 <?php echo ($step === 3) ? 'block': 'none'; ?>">
			<div class="prompt">
				We can do that. Just login with that existing account now and we'll link them up!
			</div>
			<div class="responses">
				<a href="<?php echo Route::url('index.php?option=com_users&view=logout&return=' .
					base64_encode(
						Route::url('index.php?option=com_users&view=login&reset=1&return=' .
							base64_encode(
								Route::url('index.php?option=com_users&view=login&authenticator=' . $this->hzad->authenticator, false)
							),
						false)
					)); ?>">
					<div data-step="3" class="button ok">OK</div>
				</a>
				<a href="<?php echo Route::url('index.php?option=com_users&view=link&step=2'); ?>">
					<div data-step="3" class="button previous back">Go back</div>
				</a>
			</div>
		</div>
	</div>
</section>