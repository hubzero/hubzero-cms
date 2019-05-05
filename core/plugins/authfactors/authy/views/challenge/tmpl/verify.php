<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

$this->css('enroll');
?>

<form class="authy" action="<?php echo Request::current(); ?>" method="POST">
	<div class="title">Authy</div>
	<div class="img-wrap">
		<img class="logo" src="/core/plugins/authfactors/authy/assets/img/authy_logo.svg" alt="authy logo" />
	</div>

	<div class="grouping">
		<label for="token">Token:</label>
		<input type="text" name="token" value="<?php echo User::get('token'); ?>" placeholder="1234567" />
	</div>

	<div class="grouping">
		<input type="hidden" name="action" value="verify" />
		<input type="submit" value="Submit" class="btn btn-success" />
	</div>
</form>
