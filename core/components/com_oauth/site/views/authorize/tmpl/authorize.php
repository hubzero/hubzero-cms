<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Nicholas J. Kisseberth <nkissebe@purdue.edu>
 * @copyright Copyright 2012-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

jimport('joomla.environment.request');
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