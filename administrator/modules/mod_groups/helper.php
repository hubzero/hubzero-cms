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

namespace Modules\Groups;

use Hubzero\Module\Module;

/**
 * Module class for com_groups data
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
		$type = $this->params->get('type', '1');

		switch ($type)
		{
			case '0': $this->type = 'system'; break;
			case '1': $this->type = 'hub'; break;
			case '2': $this->type = 'project'; break;
			case '3': $this->type = 'partner'; break;
		}

		$queries = array(
			'visible'    => "approved=1 AND discoverability=0",
			'hidden'     => "approved=1 AND discoverability=1",
			'closed'     => "join_policy=3",
			'invite'     => "join_policy=2",
			'restricted' => "join_policy=1",
			'open'       => "join_policy=0",
			'approved'   => "approved=1",
			'pending'    => "approved=0"
		);

		$database = \JFactory::getDBO();

		foreach ($queries as $key => $where)
		{
			$database->setQuery("SELECT count(*) FROM `#__xgroups` WHERE type='$type' AND $where");
			$this->$key = $database->loadResult();
		}

		// Last 24 hours
		$lastDay = date('Y-m-d', (time() - 24*3600)) . ' 00:00:00';

		$database->setQuery("SELECT count(*) FROM `#__xgroups` WHERE created >= '$lastDay' AND type='$type'");
		$this->pastDay = $database->loadResult();

		// Get the view
		parent::display();
	}
}
