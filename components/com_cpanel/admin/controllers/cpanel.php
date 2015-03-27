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

namespace Components\Cpanel\Admin\Controllers;

use Hubzero\Component\AdminController;

/**
 * Cpanel Controller
 */
class Cpanel extends AdminController
{
	/**
	 * Display admin control panel
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Set the template - this will display cpanel.php
		// from the selected admin template.
		\Request::setVar('tmpl', 'cpanel');

		// Display the cpanel modules
		$this->view->modules = \JModuleHelper::getModules('cpanel');

		$this->view
			->setLayout('default')  // Preserve potential view overrides
			->display();
	}
}