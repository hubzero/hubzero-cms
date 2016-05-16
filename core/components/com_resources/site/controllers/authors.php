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

use Components\Resources\Models\Orm\Resource;
use Components\Resources\Models\Author;
use Hubzero\Component\SiteController;
use Request;
use User;
use Lang;
use App;

include_once(dirname(dirname(__DIR__)) . DS . 'models' . DS . 'orm' . DS . 'resource.php');

/**
 * Controller class for contributing a tool
 */
class Authors extends SiteController
{
	/**
	 * Determines task being called and attempts to execute it
	 *
	 * @return  void
	 */
	public function execute()
	{
		if (User::isGuest())
		{
			App::abort(403, Lang::txt('COM_RESOURCES_ALERTLOGIN_REQUIRED'));
		}

		parent::execute();
	}

	/**
	 * Save one or more authors
	 *
	 * @param   integer  $show        Display author list when done?
	 * @param   integer  $id          Resource ID
	 * @param   array    $authorsNew  Authors to add
	 * @return  void
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
		$authorsNewstr = trim(Request::getVar('new_authors', '', 'post'));
		$authid   = Request::getInt('authid', 0, 'post');
		$username = Request::getVar('author', '', 'post');
		$role     = Request::getVar('role', '', 'post');

		// Turn the string into an array of usernames
		$authorsNew = empty($authorsNew) ? explode(',', $authorsNewstr) : $authorsNew;

		// Instantiate a resource/contributor association object
		$author = Author::blank();
		$author->set('subtable', 'resources');
		$author->set('subid', $id);

		// Get the last child in the ordering
		//$order = $rc->getLastOrder($id, 'resources');
		//$order = $order + 1; // new items are always last

		if (!$authid && $username)
		{
			$profile = User::getInstance($username);

			if (!$profile)
			{
				$this->setError(Lang::txt('CONTRIBUTE_NO_ID'));
				if ($show)
				{
					$this->displayTask($id);
				}
				return;
			}

			$authid = $profile->get('id');
		}

		// Was there an ID? (this will come from the author <select>)
		if ($authid)
		{
			// Check if they're already linked to this resource
			$existing = Author::oneByRelationship($id, $authid);

			if ($existing->get('id'))
			{
				$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $rc->name));
			}
			else
			{
				// Perform a check to see if they have a contributors page. If not, we'll need to make one
				$profile = User::getInstance($authid);

				if ($profile)
				{
					$author->set('authorid', $authid);
					$author->set('name', (string)$profile->get('name'));
					$author->set('role', (string)$role);
					$author->set('organization', (string)$profile->get('organization'));
					$author->save();
				}
			}
		}

		// Do we have new authors?
		if (!empty($authorsNew))
		{
			// loop through each one
			for ($i=0, $n=count($authorsNew); $i < $n; $i++)
			{
				$cid = trim($authorsNew[$i]);

				if (is_numeric($cid))
				{
					$uid = intval($cid);
				}

				$profile = User::getInstance($authid);

				// Find the user's account info
				if (!$profile)
				{
					// No account
					// This should mean we have an author that is not a site member

					$cid = trim($cid);

					// No name. Can't save record, so pass over it.
					if (!$cid)
					{
						continue;
					}

					// Check to see if they're already an author
					$author = Author::oneByName($id, $cid);

					if ($author->get('id'))
					{
						$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $cid));
						continue;
					}

					$authorid     = $author->getUserId($cid);
					$name         = $cid;
					$organization = '';
				}
				else
				{
					// Check to see if they're already an author
					$author = Author::oneByRelationship($id, $profile->get('id'));

					if ($author->get('id'))
					{
						$this->setError(Lang::txt('COM_CONTRIBUTE_USER_IS_ALREADY_AUTHOR', $author->get('name')));
						continue;
					}

					$authorid     = $profile->get('id');
					$name         = $profile->get('name');
					$organization = $profile->get('organization');
				}

				$author->set('subtable', 'resources');
				$author->set('subid', $id);
				$author->set('authorid', $authorid);
				$author->set('name', (string)$name);
				$author->set('organization', (string)$organization);
				$author->set('role', (string)$role);
				$author->save();

				// Log activity
				if ($authorid > 0)
				{
					$resource = Resource::oneOrFail($id);

					Event::trigger('system.logActivity', [
						'activity' => [
							'action'      => 'updated',
							'scope'       => 'resource',
							'scope_id'    => $resource->get('id'),
							'description' => Lang::txt('COM_RESOURCES_ACTIVITY_ENTRY_AUTHOR_ADDED', $name, '<a href="' . Route::url('index.php?option=com_resources&id=' . $resource->get('id')) . '">' . $resource->get('title') . '</a>'),
							'details'     => array(
								'title' => $resource->get('title'),
								'url'   => Route::url('index.php?option=com_resources&id=' . $resource->get('id'))
							)
						],
						'recipients' => array(
							['resource', $resource->get('id')],
							['user', $resource->get('created_by')],
							['user', $authorid]
						)
					]);
				}
			}
		}

		if ($show)
		{
			// Push through to the authors view
			$this->displayTask($id);
		}
	}

	/**
	 * Remove an author from an item
	 *
	 * @return  void
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
			return $this->displayTask();
		}

		// Ensure we have the contributor's ID ($id)
		if ($id)
		{
			$author = Author::oneByRelationship($pid, $id);

			if (!$author->destroy())
			{
				$this->setError($author->getError());
			}
			else
			{
				// Log activity
				if ($author->get('authorid') > 0)
				{
					$resource = Resource::oneOrFail($pid);

					Event::trigger('system.logActivity', [
						'activity' => [
							'action'      => 'updated',
							'scope'       => 'resource',
							'scope_id'    => $resource->get('id'),
							'description' => Lang::txt('COM_RESOURCES_ACTIVITY_ENTRY_AUTHOR_REMOVED', $name, '<a href="' . Route::url('index.php?option=com_resources&id=' . $resource->get('id')) . '">' . $resource->get('title') . '</a>'),
							'details'     => array(
								'title' => $resource->get('title'),
								'url'   => Route::url('index.php?option=com_resources&id=' . $resource->get('id'))
							)
						],
						'recipients' => array(
							['resource', $resource->get('id')],
							['user', $resource->get('created_by')],
							['user', $authorid]
						)
					]);
				}
			}
		}

		// Push through to the authors view
		$this->displayTask($pid);
	}

	/**
	 * Update information for a resource author
	 *
	 * @return  void
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
			return $this->displayTask();
		}

		// Ensure we have the contributor's ID ($id)
		if ($ids)
		{
			foreach ($ids as $id => $data)
			{
				$author = Author::oneByRelationship($pid, $id);

				if (!$author->get('id'))
				{
					continue;
				}

				$author->set('organization', $data['organization']);
				$author->set('role', $data['role']);
				if (!$author->save())
				{
					$this->setError($author->getError());
				}
			}
		}

		// Push through to the authors view
		$this->displayTask($pid);
	}

	/**
	 * Reorder the list of authors
	 *
	 * @return  void
	 */
	public function reorderTask()
	{
		// Incoming
		$id   = Request::getInt('id', 0);
		$pid  = Request::getInt('pid', 0);
		$move = Request::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_CONTRIBUTE_NO_ID'));
			return $this->displayTask($pid);
		}

		switch ($move)
		{
			case 'up':
				$move = -1;
			break;

			case 'down':
				$move = 1;
			break;
		}

		$author = Author::oneByRelationship($pid, $id);

		// Save changes
		if (!$author->move($move))
		{
			$this->setError($author->getError());
		}

		// Push through to the attachments view
		$this->displayTask($pid);
	}

	/**
	 * Display a list of authors
	 *
	 * @param   integer  $id  Resource ID
	 * @return  void
	 */
	public function displayTask($id=null)
	{
		// Incoming
		if (!$id)
		{
			$id = Request::getInt('id', 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(404, Lang::txt('CONTRIBUTE_NO_ID'));
		}

		// Get all contributors of this resource
		$resource = Resource::oneOrFail($id);

		$authors = $resource->authors()
			->ordered()
			->rows();

		// Get all roles for this resoruce type
		$roles = $resource->type()->roles()->rows();

		// Output view
		$this->view
			->set('config', $this->config)
			->set('id', $id)
			->set('contributors', $authors)
			->set('roles', $roles)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
