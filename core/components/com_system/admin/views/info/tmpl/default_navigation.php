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
defined('_JEXEC') or die;
?>
<div id="submenu-box">
	<div class="submenu-box">
		<div class="submenu-pad">
			<ul id="submenu" class="information">
				<li>
					<a href="#page-site" id="site" class="active"><?php echo Lang::txt('COM_SYSTEM_INFO_SYSTEM_INFORMATION'); ?></a>
				</li>
				<li>
					<a href="#page-phpsettings" id="phpsettings"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_SETTINGS'); ?></a>
				</li>
				<li>
					<a href="#page-config" id="config"><?php echo Lang::txt('COM_SYSTEM_INFO_CONFIGURATION_FILE'); ?></a>
				</li>
				<li>
					<a href="#page-directory" id="directory"><?php echo Lang::txt('COM_SYSTEM_INFO_DIRECTORY_PERMISSIONS'); ?></a>
				</li>
				<li>
					<a href="#page-phpinfo" id="phpinfo"><?php echo Lang::txt('COM_SYSTEM_INFO_PHP_INFORMATION'); ?></a>
				</li>
			</ul>
			<div class="clr"></div>
		</div>
	</div>
	<div class="clr"></div>
</div>
