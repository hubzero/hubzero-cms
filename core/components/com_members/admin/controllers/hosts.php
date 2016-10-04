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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Members\Admin\Controllers;

use Hubzero\Component\AdminController;
use Components\Members\Models\Member;
use Components\Members\Models\Host;
use Request;
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
		$host = Request::getVar('host', '');

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
		$host = Request::getVar('host', '');
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

