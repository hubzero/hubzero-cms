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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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