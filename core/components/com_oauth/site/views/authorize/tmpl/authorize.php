<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();
?>

<form action="<?php echo $this->form_action;?>" id="oauth_form" method="post">

	<input id="oauth_token" name="oauth_token" type="hidden" value="<?php echo $this->oauth_token;?>" />

	<fieldset class="sign-in">
		<legend>Sign in to HUBzero</legend>
		<div class="row user">
			<label for="username" tabindex="-1">Username</label>
			<input aria-required="true" autocapitalize="off" autocorrect="off" autofocus="autofocus" class="text" id="username" name="username" required="required" type="text" />
		</div>
		<div class="row password">
			<label for="password" tabindex="-1">Password</label>
			<input aria-required="true" class="password text" id="password" name="password" required="required" type="password" value="" />
		</div>
	</fieldset>

	<fieldset class="buttons">
		<legend>Authorize access to use your account?</legend>
		<input type="submit" value="Authorize app" class="submit button selected" id="allow" />
		<input type="submit" value="No, thanks" class="submit button" name="deny" id="deny" />
	</fieldset>
</form>