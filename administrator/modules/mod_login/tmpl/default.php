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

// No direct access.
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
?>
<form action="<?php echo Route::url('index.php', true, $params->get('usesecure')); ?>" method="post" id="form-login">
	<fieldset>
		<legend><?php echo Lang::txt('MOD_LOGIN_LOGIN'); ?></legend>

		<div class="input-wrap">
			<label id="mod-login-username-lbl" for="mod-login-username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
			<input name="username" id="mod-login-username" type="text" class="inputbox" size="15" />
		</div>

		<div class="input-wrap">
			<label id="mod-login-password-lbl" for="mod-login-password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
			<input name="passwd" id="mod-login-password" type="password" class="inputbox" size="15" />
		</div>

		<div class="input-wrap">
			<label id="mod-login-language-lbl" for="lang"><?php echo Lang::txt('MOD_LOGIN_LANGUAGE'); ?></label>
			<?php echo $langs; ?>
		</div>

		<div class="button-holder">
			<input type="submit" value="<?php echo Lang::txt('MOD_LOGIN_LOGIN'); ?>" />
		</div>

		<input type="hidden" name="option" value="com_login" />
		<input type="hidden" name="task" value="login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />

		<?php echo JHtml::_('form.token'); ?>
	</fieldset>
</form>
