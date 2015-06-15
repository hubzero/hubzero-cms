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

Toolbar::title(Lang::txt('COM_SYSTEM_INFO'), 'systeminfo.png');
Toolbar::help('sysinfo');

// Add specific helper files for html generation
Html::addIncludePath(dirname(JPATH_COMPONENT) . '/helpers/html');

// Load switcher behavior
Html::behavior('switcher', 'submenu');

Document::setBuffer($this->loadTemplate('navigation'), 'modules', 'submenu');
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option . '&controller=' . $this->controller); ?>" method="post" name="adminForm" id="adminForm">
	<div id="config-document">
		<div id="page-site" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('system'); ?>
				</div>
			</div>
		</div>

		<div id="page-phpsettings" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('phpsettings'); ?>
				</div>
			</div>
		</div>

		<div id="page-config" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('config'); ?>
				</div>
			</div>
		</div>

		<div id="page-directory" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('directory'); ?>
				</div>
			</div>
		</div>

		<div id="page-phpinfo" class="tab">
			<div class="noshow">
				<div class="width-100">
					<?php echo $this->loadTemplate('phpinfo'); ?>
				</div>
			</div>
		</div>
	</div>

	<div class="clr"></div>
</form>
