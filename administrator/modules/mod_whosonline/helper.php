<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2014 Purdue University. All rights reserved.
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
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\WhosOnline;

use Hubzero\Module\Module;

/**
 * Module class for showing users online
 */
class Helper extends Module
{
	/**
	 * Display module contents
	 *
	 * @return     void
	 */
	public function display()
	{
		// create objects
		$this->juser = \JFactory::getUser();

		// hides Administrator or Super Administrator from list depending on usertype
		$and = '';
		if ($this->juser->get('gid') == 24)
		{
			$and = " AND gid != 25 ";
		}

		// manager check
		if ($this->juser->get('gid') == 23)
		{
			$and  = " AND gid != 25 ";
			$and .= " AND gid != 24 ";
		}

		// get users online
		$database = \JFactory::getDBO();
		$database->setQuery(
			"SELECT username, MAX(time) as time, userid, usertype, client_id 
				FROM `#__session`
				WHERE userid != 0 $and
				GROUP BY userid, client_id
				ORDER BY time DESC"
		);
		$this->rows = $database->loadObjectList();

		// Get the view
		parent::display();
	}
}
