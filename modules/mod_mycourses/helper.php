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

namespace Modules\MyCourses;

use Hubzero\Module\Module;
use User;
use Date;

/**
 * Module class for displaying a list of courses for a user
 */
class Helper extends Module
{
	/**
	 * Get courses for a user
	 *
	 * @param   integer  $uid   User ID
	 * @param   string   $type  Membership type to return courses for
	 * @return  array
	 */
	private function _getCourses($uid, $type='all')
	{
		$db = \JFactory::getDBO();

		// Get all courses the user is a member of
		$query = "SELECT c.id, c.state, c.alias, c.title, m.enrolled, m.student, s.publish_up AS starts, s.publish_down AS ends, r.alias AS role, o.alias AS offering_alias, o.title AS offering_title, o.state AS offering_state, s.state AS section_state, s.alias AS section_alias, s.title AS section_title, s.is_default
					FROM `#__courses` AS c
					JOIN `#__courses_members` AS m ON m.course_id=c.id
					LEFT JOIN `#__courses_offerings` AS o ON o.id=m.offering_id
					LEFT JOIN `#__courses_offering_sections` AS s on s.id=m.section_id
					LEFT JOIN `#__courses_roles` AS r ON r.id=m.role_id
					WHERE c.state IN (1, 3)
					AND m.user_id=" . $db->quote($uid);

		$now = Date::toSql();

		$db->setQuery($query);

		$result = $db->loadObjectList();
		$rows = array();

		if (empty($result))
		{
			return $rows;
		}

		foreach ($result as $row)
		{
			if (is_numeric($row->offering_state) && in_array($row->offering_state, array(0, 2)))
			{
				continue;
			}

			if (is_numeric($row->section_state) && in_array($row->section_state, array(0, 2)))
			{
				continue;
			}

			if ($row->ends && $row->ends != '0000-00-00 00:00:00' && $row->ends < $now)
			{
				continue;
			}

			$rows[] = $row;
		}

		return $rows;
	}

	/**
	 * Display module contents
	 *
	 * @return  void
	 */
	public function display()
	{
		// Get the module parameters
		$this->moduleclass = $this->params->get('moduleclass');
		$this->limit = intval($this->params->get('limit', 10));

		// Get the user's groups
		$this->courses = $this->_getCourses(User::get('id'), 'all');

		// Push the module CSS to the template
		$this->css();

		require $this->getLayoutPath();
	}
}

