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
<fieldset class="adminform">
	<legend><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></legend>
	<table class="adminlist">
		<thead>
			<tr>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_SETTING'); ?>
				</th>
				<th scope="col">
					<?php echo Lang::txt('COM_SYSTEM_INFO_VALUE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_BUILT_ON'); ?>
				</th>
				<td>
					<?php echo $this->info['php'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_DATABASE_COLLATION'); ?>
				</th>
				<td>
					<?php echo $this->info['dbcollation'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PHP_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['phpversion'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEB_SERVER'); ?>
				</th>
				<td>
					<?php echo Html::system('server', $this->info['server']); ?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_WEBSERVER_TO_PHP_INTERFACE'); ?>
				</th>
				<td>
					<?php echo $this->info['sapi_name'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_JOOMLA_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['version'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_PLATFORM_VERSION'); ?>
				</th>
				<td>
					<?php echo $this->info['platform'];?>
				</td>
			</tr>
			<tr>
				<th scope="row">
					<?php echo Lang::txt('COM_SYSTEM_INFO_USER_AGENT'); ?>
				</th>
				<td>
					<?php echo htmlspecialchars($this->info['useragent']);?>
				</td>
			</tr>
		</tbody>
	</table>
</fieldset>
