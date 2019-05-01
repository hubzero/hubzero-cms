<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Members\Models\Member;
use Components\Members\Models\Host;
use Request;
use Event;
use Lang;

/**
 * Manage host entries for a member
 */
class Hosts extends AdminController
{
	/**
	 * Add a host entry for a member
	 *
	 * @return  void
	 */
	public function addTask()
	{
		// Check for request forgeries
		Request::checkToken();

		if (!User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			return $this->displayTask();
		}

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_ID'));
			return $this->displayTask();
		}

		// Load the profile
		$profile = Member::oneOrFail($id);
		$profile->disableCaching();

		// Incoming host
		$host = Request::getString('host', '');

		if (!$host)
		{
			$this->setError(Lang::txt('COM_MEMBERS_NO_HOST'));
			return $this->displayTask($id);
		}

		// Update the hosts list
		if (!Host::addUserToHost($profile->get('id'), $host))
		{
			$this->setError(Lang::txt('Failed to add host "%s"', $host));
		}

		Event::trigger('user.onAfterStoreProfile', array($profile));

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Remove a host entry for a member
	 *
	 * @return  void
	 */
	public function removeTask()
	{
		// Check for request forgeries
		Request::checkToken('get');

		if (!User::authorise('core.admin', $this->_option)
		 && !User::authorise('core.edit', $this->_option))
		{
			return $this->displayTask();
		}

		// Incoming member ID
		$id = Request::getInt('id', 0);
		if (!$id)
		{
			$this->setError(Lang::txt('MEMBERS_NO_ID'));
			return $this->displayTask();
		}

		// Load the profile
		$profile = Member::oneOrFail($id);

		// Incoming host
		$host = Request::getString('host', '');
		if (!$host)
		{
			$this->setError(Lang::txt('MEMBERS_NO_HOST'));
			return $this->displayTask($profile);
		}

		$h = Host::oneByHostAndUser($host, $id);

		if (!$h->destroy())
		{
			$this->setError(Lang::txt('MEMBERS_NO_HOST'));
		}

		Event::trigger('user.onAfterStoreProfile', array($profile));

		// Push through to the hosts view
		$this->displayTask($profile);
	}

	/**
	 * Display host entries for a member
	 *
	 * @param   object  $profile
	 * @return  void
	 */
	public function displayTask($profile=null)
	{
		// Incoming
		if (!$profile)
		{
			$id = Request::getInt('id', 0);

			$profile = Member::oneOrFail($id);
		}

		// Output the HTML
		$this->view
			->set('id', $profile->get('id'))
			->set('rows', $profile->purgeCache()->hosts)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
