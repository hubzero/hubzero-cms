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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2014 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Modules\Members;

use Hubzero\Module\Module;

/**
 * Module class for com_members data
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
		$database = \JFactory::getDBO();

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u, `#__xprofiles` AS m WHERE m.uidNumber=u.id AND m.emailConfirmed < -1");
		$this->unconfirmed = $database->loadResult();

		$database->setQuery("SELECT count(u.id) FROM `#__users` AS u, `#__xprofiles` AS m WHERE m.uidNumber=u.id AND m.emailConfirmed >= 1");
		$this->confirmed = $database->loadResult();

		$database->setQuery("SELECT count(*) FROM `#__users` WHERE registerDate >= " . $database->quote(gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00'));
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}
