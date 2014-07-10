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

namespace Modules\Answers;

use Hubzero\Module\Module;

/**
 * Module class for com_answers data
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

		$queries = array(
			'closed'  => "state=1",
			'open'    => "state=0",
			'pastDay' => "created >= " . $database->quote(gmdate('Y-m-d', (time() - 24*3600)) . ' 00:00:00')
		);

		if ($this->params->get('showMine', 0))
		{
			$juser = \JFactory::getUser();
			$this->username = $juser->get('username');

			// My Open
			$queries['myclosed'] = "state=1 AND created_by=" . $juser->get('id');

			// My Closed
			$queries['myopen']   = "state=0 AND created_by=" . $juser->get('id');
		}

		foreach ($queries as $key => $where)
		{
			$database->setQuery("SELECT count(*) FROM `#__answers_questions` WHERE $where");
			$this->$key = $database->loadResult();
		}

		// Get the view
		parent::display();
	}
}
