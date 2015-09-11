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

// No direct access.
defined('_HZEXEC_') or die();

\Hubzero\Document\Assets::addComponentStylesheet('com_users', 'userconsent.css');
?>

<header id="content-header">
	<h2><?php echo Lang::txt('COM_USERS_USERCONSENT'); ?></h2>
</header>

<section class="section consent">
	<div><?php echo Lang::txt('COM_USERS_USERCONSENT_MESSAGE'); ?></div>

	<form method="POST" action="<?php echo Route::url('index.php?option=com_users&task=user.consent'); ?>">
		<input type="hidden" name="return" value="<?php echo base64_encode(Request::current(true)); ?>" />
		<div class="actions">
			<a class="btn btn-secondary" href="/"><?php echo Lang::txt('COM_USERS_USERCONSENT_CANCEL'); ?></a>
			<button class="btn btn-success" type="submit"><?php echo Lang::txt('COM_USERS_USERCONSENT_AGREE'); ?></button>
		</div>
	</form>
</section>