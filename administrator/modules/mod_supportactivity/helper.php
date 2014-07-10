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

namespace Modules\Activityfeed;

use Hubzero\Module\Module;

/**
 * Module class for an activity feed
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

		$where = "";
		if ($start = \JRequest::getVar('start', ''))
		{
			$where = "WHERE a.created > " . $database->quote($start);
		}

		$query = "SELECT a.* FROM (
					(SELECT c.id, c.ticket, c.created, (CASE WHEN `comment` != '' THEN 'comment' ELSE 'change' END) AS 'category' FROM `#__support_comments` AS c)
					UNION
					(SELECT '0' AS id, t.id AS ticket, t.created, 'ticket' AS 'category' FROM `#__support_tickets` AS t)
				) AS a $where ORDER BY a.created DESC LIMIT 0, " . $this->params->get('limit', 25);

		$database->setQuery($query);
		$this->results = $database->loadObjectList();

		$this->feed = \JRequest::getInt('feedactivity', 0);

		if ($this->feed == 1)
		{
			ob_clean();
			foreach ($this->results as $result)
			{
				require \JModuleHelper::getLayoutPath($this->module->module, 'default_item');
			}
			exit();
		}

		parent::display();
	}
}
