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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFY'); ?></h2>
</header>

<section class="main section">
	<form action="<?php echo Route::url('index.php?option=com_members&controller=credentials&task=verifying'); ?>" method="post" name="hubForm" id="hubForm">
		<fieldset>
			<legend><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_CONFIRM_VERIFICATION_TOKEN'); ?></legend>

			<p>
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_DESCRIPTION'); ?>
			</p>
			<label for="token">
				<?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_VERIFICATION_TOKEN_LABEL'); ?>:
				<span class="required"><?php echo Lang::txt('COM_MEMBERS_CREDENTIALS_REQUIRED'); ?></span>
			</label>
			<input type="text" name="token" />
		</fieldset>
		<div class="clear"></div>

		<p class="submit"><button type="submit"><?php echo Lang::txt('Submit'); ?></button></p>
		<?php echo Html::input('token'); ?>
	</form>
</section>