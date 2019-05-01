<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
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

		if (!User::authorise('core.admin', 'com_groups')
		 && !User::authorise('core.manage', 'com_groups'))
		{
			return $this->displayTask($id);
		}

		// Incoming group table
		$tbl = Request::getString('tbl', '');
		if (!$tbl)
		{
			$this->setError(Lang::txt('COM_MEMBERS_GROUPS_NO_TABLE'));
			return $this->displayTask($id);
		}

		// Incoming group ID
		$gid = Request::getInt('gid', 0);
		if (!$gid)
		{
			$this->setError(Lang::txt('COM_MEMBERS_GROUPS_NO_ID'));
			return $this->displayTask($id);
		}

		// Load the group page
		$group = Group::getInstance($gid);

		// Make sure the user isn't already a member
		if (in_array($id, $group->get($tbl)))
		{
			$this->setError(Lang::txt('COM_MEMBERS_ALREADY_A_MEMBER_OF_GROUP'));
			return $this->displayTask($id);
		}

		// Remove the user from any other lists they may be apart of
		$group->remove('invitees', array($id));
		$group->remove('applicants', array($id));
		$group->remove('members', array($id));
		$group->remove('managers', array($id));

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

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask($id);
		}

		if (!User::authorise('core.admin', 'com_groups')
		 && !User::authorise('core.manage', 'com_groups'))
		{
			return $this->displayTask($id);
		}

		// Incoming group ID
		$gid = Request::getString('gid', '');
		if (!$gid)
		{
			$this->setError(Lang::txt('COM_MEMBERS_GROUPS_NO_ID'));
			return $this->displayTask($id);
		}

		// Load the group
		$group = Group::getInstance($gid);

		// Remove the user from any other lists they may be apart of
		$group->remove('invitees', array($id));
		$group->remove('applicants', array($id));
		$group->remove('members', array($id));
		$group->remove('managers', array($id));

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

		$options = Request::getArray('memberoption', array());

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
