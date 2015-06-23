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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

/**
 * Courses user plugin for replicating essential course info to an underlying group (for file system access)
 */
class plgUserCourses extends \Hubzero\Plugin\Plugin
{
	/**
	 * Update group and membership info in underlying 'course-' group (group type 4)
	 *
	 * Method is called anytime after a course is saved
	 *
	 * @param $course - course object
	 */
	public function onAfterStoreCourse($course)
	{
		// Get a new group object
		$group = new \Hubzero\User\Group();

		// If the course doesn't have a group id set, then we need to create a new group
		if (!$course->get('group_id'))
		{
			// Set some group info
			$group->set('cn', 'course-'.$course->cn);
			$group->create();
			$group->set('type', 4); // group type 4 = course

			// Set the new group gidNumber as the group_id in the course and update
			$course->set('group_id', $group->get('gidNumber'));
			$course->update();
		}
		else // We already have a group_id set for our course
		{
			$group->read($course->get('group_id'));
		}

		// Set the group description (in case it's been changed)
		$group->set('description', $course->get('description'));

		// Get all of the course members that are not yet group members (i.e. they need to be added to the group)
		$add = array_diff($course->get('members'), $group->get('members'));

		foreach ($add as $a)
		{
			$group->add('members', $a);
		}

		// Get all of the group members that are not members of the course (i.e. they need to be removed from the group)
		$remove = array_diff($group->get('members'), $course->get('members'));

		foreach ($remove as $r)
		{
			$group->remove('members', $r);
		}

		// Finally, update the group
		$group->update();
	}
}