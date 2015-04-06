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

defined('_JEXEC') or die;

?>
<!DOCTYPE html>
<html lang="<?php echo $this->language; ?>" dir="<?php echo $this->direction; ?>">
	<head>
		<jdoc:include type="head" />
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline.css" type="text/css" />
		<?php if ($this->direction == 'rtl') : ?>
			<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/offline_rtl.css" type="text/css" />
		<?php endif; ?>
		<link rel="stylesheet" href="<?php echo $this->baseurl ?>/templates/system/css/general.css" type="text/css" />
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
			<form action="<?php echo JRoute::_('index.php', true); ?>" method="post" id="form-login">
				<fieldset class="input">
					<p id="form-login-username">
						<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME') ?></label>
						<input name="username" id="username" type="text" class="inputbox" alt="<?php echo Lang::txt('JGLOBAL_USERNAME') ?>" size="18" />
					</p>
					<p id="form-login-password">
						<label for="passwd"><?php echo Lang::txt('JGLOBAL_PASSWORD') ?></label>
						<input type="password" name="password" class="inputbox" size="18" alt="<?php echo Lang::txt('JGLOBAL_PASSWORD') ?>" id="passwd" />
					</p>
					<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
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
					<?php echo JHtml::_('form.token'); ?>
				</fieldset>
			</form>
		</div>
	</body>
</html>
