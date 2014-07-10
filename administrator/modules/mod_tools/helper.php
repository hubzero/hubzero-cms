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

namespace Modules\Tools;

use Hubzero\Module\Module;

/**
 * Module class for com_tools data
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
		$this->total      = 0;
		$this->registered = 0;
		$this->created    = 0;
		$this->uploaded   = 0;
		$this->installed  = 0;
		$this->updated    = 0;
		$this->approved   = 0;
		$this->published  = 0;
		$this->retired    = 0;
		$this->abandoned  = 0;

		$database = \JFactory::getDBO();
		$database->setQuery(
			"SELECT f.state FROM `#__tool` as f
				JOIN `#__tool_version` AS v ON f.id=v.toolid AND v.state=3
				WHERE f.id != 0 ORDER BY f.state_changed DESC"
		);
		$this->data = $database->loadObjectList();
		if ($this->data)
		{
			$this->total = count($this->data);

			foreach ($this->data as $data)
			{
				switch ($data->state)
				{
					case 1: $this->registered++; break;
					case 2: $this->created++;    break;
					case 3: $this->uploaded++;   break;
					case 4: $this->installed++;  break;
					case 5: $this->updated++;    break;
					case 6: $this->approved++;   break;
					case 7: $this->published++;  break;
					case 8: $this->retired++;    break;
					case 9: $this->abandoned++;  break;
				}
			}
		}

		// Get the view
		parent::display();
	}
}
