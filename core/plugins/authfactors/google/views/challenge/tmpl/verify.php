<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('enroll');
?>

<form class="gauth2fa" action="<?php echo Request::current(); ?>" method="POST">
	<div class="title">Google Authenticator</div>
    <p class="subtitle"> Enter the currnet code from the Google Authenticator App.</p>

	<div class="grouping">
		<label for="token">Code:</label>
		<input type="text" name="token" value="<?php echo User::get('token'); ?>" placeholder="######" />
	</div>

	<div class="grouping">
		<input type="hidden" name="action" value="verify" />
		<input type="submit" value="Submit" class="btn btn-success" />
	</div>
</form>
