<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

$this->css('register.css');
?>
<header id="content-header">
	<h2><?php echo $this->title; ?></h2>
</header><!-- / #content-header -->

<section class="main section">
	<div class="grid">
		<div class="col span-half">
			<div class="<?php echo $this->getError() ? 'error' : 'success'; ?>-message">
				<p><?php echo $this->getError() ? Lang::txt('COM_MEMBERS_REGISTER_ERROR_OCCURRED') : Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_CREATED'); ?></p>
			</div>
		</div><!-- / .col span-half -->
		<div class="col span-half omega">
			<?php if ($this->getError()) { ?>
				<p class="error"><?php echo $this->getError(); ?></p>
			<?php } else if ($this->xprofile->get('activation') < 0){ ?>
				<div class="account-activation">
					<div class="instructions">
						<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_CREATED_MESSAGE', $this->sitename, \Hubzero\Utility\Str::obfuscate($this->xprofile->get('email'))); ?></p>
						<ol>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_FIND_EMAIL'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_ACTIVATE'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_LOGIN'); ?></li>
							<li><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_SUCCESS'); ?></li>
						</ol>
					</div>
					<div class="notes">
						<p><?php echo Lang::txt('COM_MEMBERS_REGISTER_ACCOUNT_INSTRUCT_NOTE', Route::url('index.php?option=com_support')); ?></p>
					</div>
				</div>
			<?php } ?>
		</div><!-- / .col span-half omega -->
	</div><!-- / .grid -->
</section><!-- / .main section -->
