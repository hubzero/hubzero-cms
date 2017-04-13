<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Ilya Shunko
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>"> <!--<![endif]-->
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline.css" type="text/css" />
<?php if ($this->direction == 'rtl') : ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline_rtl.css" type="text/css" />
<?php endif; ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/system.css" type="text/css" />
	</head>
	<body>
		<jdoc:include type="message" />
		<div id="frame" class="outline">
			<h1>
				<?php echo Config::get('sitename'); ?>
			</h1>
			<p>
				<?php echo Config::get('offline_message'); ?>
			</p>
<?php if (Plugin::isEnabled('authentication', 'openid')) : ?>
			<?php Html::asset('script', 'openid.js'); ?>
<?php endif; ?>
			<form action="<?php echo Route::url('index.php?option=com_user'); ?>" method="post" name="login" id="form-login">
				<fieldset class="input">
					<p id="form-login-username">
						<label for="username"><?php echo Lang::txt('Username') ?></label><br />
						<input name="username" id="username" type="text" class="inputbox" alt="<?php echo Lang::txt('Username') ?>" size="18" />
					</p>
					<p id="form-login-password">
						<label for="passwd"><?php echo Lang::txt('Password') ?></label><br />
						<input type="password" name="passwd" class="inputbox" size="18" alt="<?php echo Lang::txt('Password') ?>" id="passwd" />
					</p>
					<p id="form-login-remember">
						<label for="remember"><?php echo Lang::txt('Remember me') ?></label>
						<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo Lang::txt('Remember me') ?>" id="remember" />
					</p>
					<input type="submit" name="Submit" class="button" value="<?php echo Lang::txt('LOGIN') ?>" />
				</fieldset>
				<input type="hidden" name="option" value="com_user" />
				<input type="hidden" name="task" value="login" />
				<input type="hidden" name="return" value="<?php echo base64_encode(Request::base()) ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>
	</body>
</html>