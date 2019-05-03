<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access.
defined('_HZEXEC_') or die();
?>

<section class="main section">
	<h3>Would you like to completely log out of your <?php echo $this->display_name; ?> account?</h2>
	<p>
		Your <?php echo $this->sitename; ?> session has ended.
	</p>
	<p>
		If you would like to end all <?php echo $this->display_name; ?> account shared sessions as well, you may do so now.
	</p>
	<p>
		<a class="logout btn" href="<?php echo Route::url('index.php?option=com_users&task=user.logout&sso=all&authenticator=' . $this->authenticator); ?>">
			End all <?php echo $this->display_name; ?> account sessions!
		</a>
	</p>
	<p>
		<a class="home btn" href="<?php echo Route::url('index.php?option=com_users&task=user.logout&sso=none&authenticator=' . $this->authenticator .'&return=' . Request::base()); ?>">
			Leave other <?php echo $this->display_name; ?> account sessions untouched.
		</a>
	</p>
</section>
