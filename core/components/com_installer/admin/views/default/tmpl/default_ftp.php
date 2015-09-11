<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

// no direct access
defined('_HZEXEC_') or die();
?>
<fieldset title="<?php echo Lang::txt('COM_INSTALLER_MSG_DESCFTPTITLE'); ?>">
	<legend><?php echo Lang::txt('COM_INSTALLER_MSG_DESCFTPTITLE'); ?></legend>

	<?php echo Lang::txt('COM_INSTALLER_MSG_DESCFTP'); ?>

	<?php if ($this->ftp instanceof Exception): ?>
		<p><?php echo Lang::txt($this->ftp->getMessage()); ?></p>
	<?php endif; ?>

	<table class="adminform">
		<tbody>
			<tr>
				<td width="120">
					<label for="username"><?php echo Lang::txt('JGLOBAL_USERNAME'); ?></label>
				</td>
				<td>
					<input type="text" id="username" name="username" class="input_box" size="70" value="" />
				</td>
			</tr>
			<tr>
				<td width="120">
					<label for="password"><?php echo Lang::txt('JGLOBAL_PASSWORD'); ?></label>
				</td>
				<td>
					<input type="password" id="password" name="password" class="input_box" size="70" value="" />
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
