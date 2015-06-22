<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

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
					<input type="submit" class="btn" value="<?php echo Lang::txt('MOD_LOGIN_LOGIN'); ?>" />
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