<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$this->js('setpassword')
     ->css('setpassword');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_SET_PASSWORD'); ?></h2>
</header>

<section class="main section">
	<p class="error error-message"></p>
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=settingpassword'); ?>" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_NEW_PASSWORD'); ?></legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD_DESCRIPTION'); ?>
			</p>
			<label for="password1">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD1_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="password" name="password1" id="newpass" tabindex="1" />

			<label for="password2">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_PASSWORD2_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="password" name="password2" tabindex="2" />

			<?php if (count($this->password_rules) > 0) : ?>
				<ul id="passrules">
					<?php foreach ($this->password_rules as $rule) : ?>
						<?php if (!empty($rule)) : ?>
							<li class="empty"><?php echo $rule; ?></li>
						<?php endif; ?>
					<?php endforeach; ?>
				</ul>
			<?php endif; ?>
		</fieldset>
		<div class="clear"></div>

		<input type="hidden" id="pass_no_html" name="no_html" value="0" />
		<p class="submit">
			<button type="submit" id="password-change-save">
				<?php echo Lang::txt('Submit'); ?>
			</button>
		</p>
		<?php echo JHTML::_('form.token'); ?>
	</form>
</section>