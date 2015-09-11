<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
		$db = \App::get('db');

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

		require $this->getLayoutPath();
	}
}

