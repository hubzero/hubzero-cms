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

namespace Modules\Submenu;

use Hubzero\Module\Module;

/**
 * Module class for rendering a submenu
 */
class Helper extends Module
{
	/**
	 * Get the items of the submenu and display them.
	 *
	 * @return  void
	 */
	public function display()
	{
		if (!\App::isAdmin() || !class_exists('\\Submenu'))
		{
			return;
		}

		// Initialise variables.
		$list = \Submenu::getItems();

		if (!is_array($list) || !count($list))
		{
			return;
		}

		require $this->getLayoutPath($this->params->get('layout', 'default'));
	}
}
