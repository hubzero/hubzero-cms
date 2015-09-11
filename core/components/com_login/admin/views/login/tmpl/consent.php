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

$this->css('userconsent');
?>

<section class="userconsent">
	<div class="wrap">
		<div class="title">
			<h2><?php echo Lang::txt('COM_LOGIN_USERCONSENT'); ?></h2>
		</div>

		<div><?php echo Lang::txt('COM_LOGIN_USERCONSENT_MESSAGE'); ?></div>

		<form method="POST" action="<?php echo Route::url('index.php?option=com_login&task=grantconsent'); ?>">
			<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
			<div class="actions">
				<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_LOGIN_USERCONSENT_AGREE'); ?></button>
				<a class="btn btn-secondary" href="/"><?php echo Lang::txt('COM_LOGIN_USERCONSENT_CANCEL'); ?></a>
			</div>
		</form>
	</div>
</section>