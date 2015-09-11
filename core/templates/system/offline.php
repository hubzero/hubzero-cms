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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

defined('_HZEXEC_') or die();
?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>" class="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" />
		<link rel="stylesheet" type="text/css" href="<?php echo $this->baseurl ?>/templates/system/css/offline.css" />
	</head>
	<body>
		<jdoc:include type="message" />
		<div id="frame" class="outline">
			<?php if (Config::get('offline_image') && file_exists(Config::get('offline_image'))) : ?>
				<img src="<?php echo Config::get('offline_image'); ?>" alt="<?php echo htmlspecialchars(Config::get('sitename')); ?>" />
			<?php endif; ?>
			<h1>
				<?php echo htmlspecialchars(Config::get('sitename')); ?>
			</h1>
			<?php if (Config::get('display_offline_message', 1) == 1 && str_replace(' ', '', Config::get('offline_message')) != ''): ?>
				<p>
					<?php echo Config::get('offline_message'); ?>
				</p>
			<?php elseif (Config::get('display_offline_message', 1) == 2 && str_replace(' ', '', Lang::txt('JOFFLINE_MESSAGE')) != ''): ?>
				<p>
					<?php echo Lang::txt('JOFFLINE_MESSAGE'); ?>
				</p>
			<?php  endif; ?>
			<form action="<?php echo Route::url('index.php', true); ?>" method="post" id="form-login">
				<fieldset class="input">
					<p id="form-login-username">
						<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME') ?></label>
						<input name="username" id="username" type="text" class="inputbox" alt="<?php echo Lang::txt('JGLOBAL_USERNAME') ?>" size="18" />
					</p>
					<p id="form-login-password">
						<label for="passwd"><?php echo Lang::txt('JGLOBAL_PASSWORD') ?></label>
						<input type="password" name="password" class="inputbox" size="18" alt="<?php echo Lang::txt('JGLOBAL_PASSWORD') ?>" id="passwd" />
					</p>
					<?php if (Plugin::isEnabled('system', 'remember')) : ?>
					<p id="form-login-remember">
						<label for="remember"><?php echo Lang::txt('JGLOBAL_REMEMBER_ME') ?></label>
						<input type="checkbox" name="remember" class="inputbox" value="yes" alt="<?php echo Lang::txt('JGLOBAL_REMEMBER_ME') ?>" id="remember" />
					</p>
					<?php  endif; ?>
					<p id="submit-buton">
						<label>&nbsp;</label>
						<input type="submit" name="Submit" class="button login" value="<?php echo Lang::txt('JLOGIN') ?>" />
					</p>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.login" />
					<input type="hidden" name="return" value="<?php echo base64_encode(Request::base()) ?>" />
					<?php echo Html::input('token'); ?>
				</fieldset>
			</form>
		</div>
	</body>
</html>