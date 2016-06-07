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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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

		$this->view
			->setLayout('default')  // Preserve potential view overrides
			->display();
	}

	/**
	 * Display a specific module
	 *
	 * @return  void
	 */
	public function moduleTask()
	{
		$this->view
			->setLayout('module')  // Preserve potential view overrides
			->display();
	}
}