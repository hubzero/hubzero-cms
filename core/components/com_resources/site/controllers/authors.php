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

namespace Components\Resources\Site\Controllers;

use Components\Resources\Tables\Resource;
use Components\Resources\Tables\Contributor;
use Components\Resources\Tables\Contributor\Role;
use Components\Resources\Tables\Contributor\RoleType;
use Components\Resources\Helpers\Helper;
use Hubzero\Component\SiteController;
use Hubzero\User\Profile;

require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'resource.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'type.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'assoc.php');
require_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'contributor.php');
require_once(dirname(dirname(__DIR__)) . DS . 'helpers' . DS . 'helper.php');

/**
 * Controller class for contributing a tool
 */
class Authors extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return     void
	 */
	public function execute()
	{
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('You must be logged in to access.'));
			return;
		}

		// Load the com_resources component config
		$rconfig = Component::params('com_resources');
		$this->rconfig = $rconfig;

		parent::execute();
	}

	/**
	 * Save one or more authors
	 *
	 * @param      integer $show       Display author list when done?
	 * @param      integer $id         Resource ID
	 * @param      array   $authorsNew Authors to add
	 * @return     void
	 */
	public function saveTask($show = 1, $id = 0, $authorsNew = array())
	{
		// Incoming resource ID
		if (!$id)
		{
			$id = Request::getInt('pid', 0);
		}
		if (!$id)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			if ($show)
			{
				$this->displayTask($id);
			}
			return;
		}

		// Incoming authors
		$authid = Request::getInt('authid', 0, 'post');
		$authorsNewstr = trim(Request::getVar('new_authors', '', 'post'));
		$role = Request::getVar('role', '', 'post');

		// Turn the string into an array of usernames
		$authorsNew = empty($authorsNew) ? explode(',', $authorsNewstr) : $authorsNew;

		// Instantiate a resource/contributor association object
		$rc = new Contributor($this->database);
		$rc->subtable = 'resources';
		$rc->subid = $id;

		// Get the last child in the ordering
		$order = $rc->getLastOrder($id, 'resources');
		$order = $order + 1; // new items are always last

		if (!$authid && isset($_POST['author']))
		{
			$this->database->setQuery('SELECT id FROM `#__users` WHERE username = ' . $this->database->quote($_POST['author']));
			$authid = $this->database->loadResult();
		}

		// Was there an ID? (this will come from the author <select>)
		if ($authid)
		{
			// Check if they're already linked to this resource
			$rc->loadAssociation($authid, $id, 'resources');
			if ($rc->authorid)
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $rc->name));
			}
			else
			{
				// Perform a check to see if they have a contributors page. If not, we'll need to make one
				$xprofile = new Profile();
				$xprofile->load($authid);
				if ($xprofile)
				{
					$this->_authorCheck($authid);

					// New record
					$rc->authorid = $authid;
					$rc->ordering = $order;
					$rc->name = addslashes($xprofile->get('name'));
					$rc->role = addslashes($role);
					$rc->organization = addslashes($xprofile->get('organization'));
					$rc->createAssociation();

					$order++;
				}
			}
		}
		$xprofile = null;
		// Do we have new authors?
		if (!empty($authorsNew))
		{
			jimport('joomla.user.helper');

			// loop through each one
			for ($i=0, $n=count($authorsNew); $i < $n; $i++)
			{
				$cid = trim($authorsNew[$i]);

				if (is_numeric($cid))
				{
					$uid = intval($cid);
				}
				else
				{
					// Find the user's account info
					$uid = \JUserHelper::getUserId(strtolower($cid));
					if (!$uid)
					{
						$cid = addslashes(trim($cid));
						// No account
						// This should mean we have an author that is not a site member
						$rcc = new Contributor($this->database);
						// Check to see if they're already an author
						$rcc->loadAssociation($cid, $id, 'resources');
						if ($rcc->authorid)
						{
							$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $cid));
							continue;
						}
						// No name. Can't save record, so pass over it.
						if (!trim($cid))
						{
							continue;
						}

						$rcc->subtable = 'resources';
						$rcc->subid    = $id;
						$rcc->authorid = $rcc->getUserId($cid);
						$rcc->ordering = $order;
						$rcc->name     = $cid;
						$rcc->role     = addslashes($role);
						$rcc->createAssociation();
						//$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
						$order++;
						continue;
					}
				}

				// We should only get to this part if the author is also a site member
				$user = User::getInstance($uid);
				if (!is_object($user))
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
					continue;
				}

				$uid = $user->get('id');

				if (!$uid)
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
					continue;
				}

				// Check if they're already linked to this resource
				$rcc = new Contributor($this->database);
				$rcc->loadAssociation($uid, $id, 'resources');
				if ($rcc->authorid)
				{
					$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $rcc->name));
					continue;
				}

				$this->_authorCheck($uid);

				$xprofile = Profile::getInstance($uid);
				$rcc->subtable     = 'resources';
				$rcc->subid        = $id;
				$rcc->authorid     = $uid;
				$rcc->ordering     = $order;
				$rcc->name         = $xprofile->get('name');
				$rcc->role         = $role;
				$rcc->organization = $xprofile->get('organization');
				if (!$rcc->createAssociation())
				{
					$this->setError($rcc->getError());
				}

				$order++;
			}
		}

		if ($show)
		{
			// Push through to the authors view
			$this->displayTask($id);
		}
	}

	/**
	 * Split a user's name into its parts if not already done
	 *
	 * @param      integer $id User ID
	 * @return     void
	 */
	private function _authorCheck($id)
	{
		$xprofile = Profile::getInstance($id);
		if ($xprofile->get('givenName') == ''
		 && $xprofile->get('middleName') == ''
		 && $xprofile->get('surname') == '')
		{
			$bits = explode(' ', $xprofile->get('name'));
			$xprofile->set('surname', array_pop($bits));
			if (count($bits) >= 1)
			{
				$xprofile->set('givenName', array_shift($bits));
			}
			if (count($bits) >= 1)
			{
				$xprofile->set('middleName', implode(' ', $bits));
			}
		}
	}

	/**
	 * Remove an author from an item
	 *
	 * @return     void
	 */
	public function removeTask()
	{
		// Incoming
		$id  = Request::getInt('id', 0);
		$pid = Request::getInt('pid', 0);

		// Ensure we have a resource ID ($pid) to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Ensure we have the contributor's ID ($id)
		if ($id)
		{
			$rc = new Contributor($this->database);
			if (!$rc->deleteAssociation($id, $pid, 'resources'))
			{
				$this->setError($rc->getError());
			}
		}

		// Push through to the authors view
		$this->displayTask($pid);
	}

	/**
	 * Update information for a resource author
	 *
	 * @return     void
	 */
	public function updateTask()
	{
		// Incoming
		$ids = Request::getVar('authors', array(), 'post');
		$pid = Request::getInt('pid', 0);

		// Ensure we have a resource ID ($pid) to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Ensure we have the contributor's ID ($id)
		if ($ids)
		{
			foreach ($ids as $id => $data)
			{
				$rc = new Contributor($this->database);
				$rc->loadAssociation($id, $pid, 'resources');
				$rc->organization = $data['organization'];
				$rc->role = $data['role'];
				$rc->updateAssociation();
			}
		}

		// Push through to the authors view
		$this->displayTask($pid);
	}

	/**
	 * Reorder the list of authors
	 *
	 * @return     void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$pid  = Request::getInt('pid', 0);
		$move = 'order' . Request::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Get the element moving down - item 1
		$author1 = new Contributor($this->database);
		$author1->loadAssociation($id, $pid, 'resources');

		// Get the element directly after it in ordering - item 2
		$author2 = clone($author1);
		$author2->getNeighbor($move);

		switch ($move)
		{
			case 'orderup':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $author2->ordering;
				$orderdn = $author1->ordering;

				$author1->ordering = $orderup;
				$author2->ordering = $orderdn;
			break;

			case 'orderdown':
				// Switch places: give item 1 the position of item 2, vice versa
				$orderup = $author1->ordering;
				$orderdn = $author2->ordering;

				$author1->ordering = $orderdn;
				$author2->ordering = $orderup;
			break;
		}

		// Save changes
		$author1->updateAssociation();
		$author2->updateAssociation();

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of authors
	 *
	 * @param      integer $id Resource ID
	 * @return     void
	 */
	public function displayTask($id=null)
	{
		$this->view->setLayout('display');

		// Incoming
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			throw new Exception(Lang::txt('CONTRIBUTE_NO_ID'), 500);
		}

		// Get all contributors of this resource
		$helper = new Helper($id, $this->database);
		$helper->getCons();

		// Get a list of all existing contributors
		include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
		include_once(PATH_CORE . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

		include_once(dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'contributor' . DS . 'roletype.php');

		$resource = new Resource($this->database);
		$resource->load($id);

		$rt = new RoleType($this->database);

		// Output HTML
		$this->view->config = $this->config;
		$this->view->contributors = $helper->_contributors;
		$this->view->id = $id;

		$this->view->roles = $rt->getRolesForType($resource->type);

		foreach ($this->getErrors() as $error)
		{
			$this->view->setError($error);
		}

		$this->view->display();
	}
}
