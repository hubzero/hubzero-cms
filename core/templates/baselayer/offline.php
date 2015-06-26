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
 * @author    Ilya Shunko
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

?>
<!DOCTYPE html>
<!--[if lt IE 7 ]> <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie6"> <![endif]-->
<!--[if IE 7 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie7"> <![endif]-->
<!--[if IE 8 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie8"> <![endif]-->
<!--[if IE 9 ]>    <html dir="<?php echo $this->direction; ?>" lang="<?php echo $this->language; ?>" class="ie9"> <![endif]-->
<!--[if (gt IE 9)|!(IE)]><!--> <html dir="<?php echo $this->direction; ?>" lang="<?php echo  $this->language; ?>"> <!--<![endif]-->
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
			<img src="images/joomla_logo_black.jpg" alt="Joomla! Logo" align="middle" />
			<h1>
				<?php echo Config::get('sitename'); ?>
			</h1>
			<p>
				<?php echo Config::get('offline_message'); ?>
			</p>
<?php if (Plugin::isEnabled('authentication', 'openid')) : ?>
			<?php Html::asset('script', 'openid.js'); ?>
<?php endif; ?>
			<form action="index.php" method="post" name="login" id="form-login">
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