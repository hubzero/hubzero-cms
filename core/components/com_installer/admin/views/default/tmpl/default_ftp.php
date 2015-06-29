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
