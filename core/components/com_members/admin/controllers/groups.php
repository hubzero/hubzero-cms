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

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Hubzero\User\Group;
use Request;
use Lang;
use User;
use App;

/**
 * Manage a member's group memberships
 */
class Groups extends AdminController
{
	/**
	 * Add a member to a group
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken();

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask($id);
		}

		// Incoming group table
		$tbl = Request::getVar('tbl', '');
		if (!$tbl)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_GROUP_TABLE'));
			return $this->displayTask($id);
		}

		// Incoming group ID
		$gid = Request::getInt('gid', 0);
		if (!$gid)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_GROUP_ID'));
			return $this->displayTask($id);
		}

		// Load the group page
		$group = Group::getInstance($gid);

		// Add the user to the group table
		$group->add($tbl, array($id));
		if ($tbl == 'managers')
		{
			// Ensure they're added to the members list as well if they're a manager
			$group->add('members', array($id));
		}

		$group->update();

		// Push through to the groups view
		$this->displayTask($id);
	}

	/**
	 * Remove member(s) from a group
	 * Disallows removal of last manager (group must have at least one)
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$gid = Request::getVar('gid', '');

		// Load the group page
		$group = Group::getInstance($gid);

		// Get all the group's managers
		$managers = $group->get('managers');

		// Get all the group's members
		$members = $group->get('members');

		$users_mem = array();
		$users_man = array();

		// User ID
		$id = Request::getInt('id', 0);

		// Ensure we found an account
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_MEMBERS_NOT_FOUND'));
		}

		if (in_array($id, $members))
		{
			$users_mem[] = $id;
		}

		if (in_array($id, $managers))
		{
			$users_man[] = $id;
		}

		// Remove users from members list
		$group->remove('members', $users_mem);

		// Remove users from managers list
		$group->remove('managers', $users_man);

		// Save changes
		$group->update();

		// Push through to the groups view
		$this->displayTask($id);
	}

	/**
	 * Update member option
	 *
	 * @return  void
	 */
	public function updateTask()
	{
		// Check for request forgeries
		Request::checkToken(['get', 'post']);

		$options = Request::getVar('memberoption', array());

		// User ID
		$id = Request::getInt('id', 0);

		// Ensure we found an account
		if (!$id)
		{
			App::abort(404, Lang::txt('COM_MEMBERS_NOT_FOUND'));
		}

		$db = App::get('db');

		foreach ($options as $key => $option)
		{
			$db->setQuery("UPDATE `#__xgroups_memberoption` SET `optionvalue`=" . $db->quote($option) . " WHERE `id`=" . $db->quote($key));
			$db->query();
		}

		// Push through to the groups view
		$this->displayTask($id);
	}

	/**
	 * Display all the groups a member is apart of
	 *
	 * @param   integer  $id  Member ID to lookup
	 * @return  void
	 */
	public function displayTask($id=0)
	{
		// Incoming
		$id = $id ? $id : Request::getInt('id', 0);

		// Get a list of all groups
		$rows = \Hubzero\User\Group::find(array(
			'type'       => array('all'),
			'limit'      => 'all',
			'search'     => '',
			'fields'     => array('cn', 'description', 'published', 'gidNumber', 'type'),
			'sortby'     => 'title',
			'authorized' => 'admin'
		));

		// Output the HTML
		$this->view
			->set('id', $id)
			->set('rows', $rows)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}

