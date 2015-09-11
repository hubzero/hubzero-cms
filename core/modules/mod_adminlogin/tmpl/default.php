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

defined('_HZEXEC_') or die;

$this->css('login')
     ->css('providers', 'com_users')
     ->js('login');

Html::behavior('keepalive');
?>

<div class="hz_user">
	<div class="auth">
		<div class="default" style="display:<?php echo count($authenticators) == 0 ? 'none' : 'block'; ?>;">
			<div class="instructions"><?php echo Lang::txt('COM_LOGIN_CHOOSE_METHOD'); ?></div>
			<div class="options">
				<?php foreach ($authenticators as $a) : ?>
					<a class="<?php echo $a['name']; ?> account" href="<?php echo Route::url('index.php?option=com_login&task=display&authenticator=' . $a['name'] . $returnQueryString); ?>">
						<div class="signin"><?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_METHOD', $a['display']); ?></div>
					</a>
				<?php endforeach; ?>
			</div>
			<?php if (isset($basic) && $basic) : ?>
				<div class="or"></div>
				<div class="local">
					<a href="#">
						<?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_ACCOUNT', $site_display); ?>
					</a>
				</div>
			<?php endif; ?>
		</div>
		<div class="hz" style="display:<?php echo count($authenticators) == 0 ? 'block' : 'none'; ?>;">
			<form action="<?php echo Route::url('index.php', true, true); ?>" method="post" class="login_form">
				<label id="mod-login-username-lbl" for="mod-login-username">
					<span><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></span>
					<input name="username" id="mod-login-username" class="input-username" type="text" size="15" placeholder="<?php echo Lang::txt('JGLOBAL_USERNAME'); ?>" />
				</label>

				<label id="mod-login-password-lbl" for="mod-login-password">
					<span><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></span>
					<input name="passwd" id="mod-login-password" class="input-password" type="password" size="15" placeholder="<?php echo Lang::txt('JGLOBAL_PASSWORD'); ?>" />
				</label>

				<div class="button-holder">
					<input type="submit" class="btn" value="<?php echo Lang::txt('MOD_ADMINLOGIN_LOGIN'); ?>" />
				</div>

				<?php if (count($authenticators) > 0) : ?>
					<div class="or"></div>
					<div class="multi-auth">
						<a href="#">
							<?php echo Lang::txt('COM_LOGIN_SIGN_IN_WITH_OTHER'); ?>
						</a>
					</div>
				<?php endif; ?>

				<input type="hidden" name="option"        value="com_login" />
				<input type="hidden" name="authenticator" value="hubzero" />
				<input type="hidden" name="task"          value="login" />
				<input type="hidden" name="return"        value="<?php echo $return; ?>" />
				<input type="hidden" name="freturn"       value="<?php echo $freturn; ?>" />
				<?php echo Html::input('token'); ?>
			</form>
		</div>
	</div>
</div>