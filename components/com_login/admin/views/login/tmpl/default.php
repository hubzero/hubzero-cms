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

// No direct access.
defined('_JEXEC') or die;

// Get the login modules
// If you want to use a completely different login module change the value of name
// in your layout override.

$loginmodule = \Components\Login\Models\Login::getLoginModule('mod_login');

echo JModuleHelper::renderModule($loginmodule, array('style' => 'rounded', 'id' => 'section-box'));


//Get any other modules in the login position.
//If you want to use a different position for the modules, change the name here in your override.
$modules = JModuleHelper::getModules('login');

foreach ($modules as $module)
{
	// Render the login modules
	if ($module->module != 'mod_login')
	{
		echo JModuleHelper::renderModule($module, array('style' => 'rounded', 'id' => 'section-box'));
	}
}
