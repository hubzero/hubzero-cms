<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();

$this->css('login.css');
?>

<section class="main section">
	<h3><?php echo Lang::txt('Would you like to completely log out of your %s account?', $this->display_name); ?></h2>
	<p>
		<?php echo Lang::txt('Your %s session has ended.', $this->sitename); ?>
	</p>
	<p>
		<?php echo Lang::txt('If you would like to end all %s account shared sessions as well, you may do so now.', $this->display_name); ?>
	</p>
	<p>
		<a class="logout btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=logout&sso=all&authenticator=' . $this->authenticator); ?>">
			<?php echo Lang::txt('End all %s account sessions!', $this->display_name); ?>
		</a>
	</p>
	<p>
		<a class="home btn" href="<?php echo Route::url('index.php?option=' . $this->option . '&task=logout&sso=none&authenticator=' . $this->authenticator .'&return=' . Request::base()); ?>">
			<?php echo Lang::txt('Leave other %s account sessions untouched.', $this->display_name); ?>
		</a>
	</p>
</section>
