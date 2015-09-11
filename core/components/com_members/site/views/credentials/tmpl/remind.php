<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=reminding'); ?>" method="post" name="hubForm" id="hubForm">
		<div class="explaination">
			<p class="info">
				If you already know your username, and only need your password reset,
				<a href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>">go here now</a>.
			</p>
		</div>
		<fieldset>
			<legend>Recover Username(s)</legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_DESCRIPTION'); ?>
			</p>
			<label for="email">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REMIND_EMAIL_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="email" />

			<div class="help">
				<h4>What if I have also lost my password?</h4>
				<p>
					Fill out this form to retrieve your username(s). Then go to the 
					<a href="<?php echo Route::url('index.php?option=com_members&task=reset'); ?>">password reset page</a>.
				</p>

				<h4>What if I have multiple accounts?</h4>
				<p>
					All accounts registered to your email address will be located, and you will be given a
					list of all of those usernames.
				</p>

				<h4>What if this cannot find my account?</h4>
				<p>
					It is possible you registered under a different email address.  Please try any other email
					addresses you have.
				</p>
			</div>
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo Html::input('token'); ?>
	</form>
</section>