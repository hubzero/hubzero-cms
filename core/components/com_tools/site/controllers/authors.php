<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Site\Controllers;

use Hubzero\Component\SiteController;
use Components\Resources\Models\Entry;
use Components\Resources\Models\Author;
use Component;
use Request;
use Route;
use Lang;
use User;
use App;

require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'tool.php';
require_once dirname(dirname(__DIR__)) . DS . 'tables' . DS . 'version.php';
require_once Component::path('com_resources') . DS . 'models' . DS . 'entry.php';

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
			// Redirect to home page
			App::redirect(
				$this->config->get('contribtool_redirect', '/home')
			);
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
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
			if ($show)
			{
				$this->displayTask($id);
			}
			return;
		}

		// Incoming authors
		$authorsNewstr = trim(Request::getString('new_authors', '', 'post'));
		$authid   = Request::getInt('authid', 0, 'post');
		$username = Request::getString('author', '', 'post');
		$role     = Request::getString('role', '', 'post');

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
				$this->setError(Lang::txt('USER_IS_ALREADY_AUTHOR', $authid));
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
				else
				{
					$cid = strtolower($cid);
					// Find the user's account info
					$uid = User::oneByUsername($cid)->get('id');

					if (!$uid)
					{
						$this->setError(Lang::txt('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
						continue;
					}
				}

				$profile = User::getInstance($uid);

				// Find the user's account info
				if (!$profile->get('id'))
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
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
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
		$ids = Request::getArray('authors', array(), 'post');
		$pid = Request::getInt('pid', 0);

		// Ensure we have a resource ID ($pid) to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_COM_CONTRIBUTE_NO_ID'));
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
		$move = Request::getWord('move', 'down');

		// Ensure we have an ID to work with
		if (!$id)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			return $this->displayTask($pid);
		}

		// Ensure we have a parent ID to work with
		if (!$pid)
		{
			$this->setError(Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
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
			$id = Request::getInt('rid', 0);
		}

		// Ensure we have an ID to work with
		if (!$id)
		{
			App::abort(500, Lang::txt('COM_TOOLS_CONTRIBUTE_NO_ID'));
		}

		$version = Request::getString('version', 'dev');

		// Get all contributors of this resource
		$resource = Entry::oneOrFail($id);

		if ($version == 'dev')
		{
			$authors = $resource->authors()
				->ordered()
				->rows();
		}
		else
		{
			$obj = new \Components\Tools\Tables\Tool($this->database);
			$toolname = $obj->getToolnameFromResource($id);

			$objV = new \Components\Tools\Tables\Version($this->database);
			$revision = $objV->getCurrentVersionProperty($toolname, 'revision');

			$resource->set('revision', $revision);
			$authors = $resource->contributors('tool');
		}

		// Get all roles for this resoruce type
		$roles = $resource->type->roles()->rows();

		$this->view
			->set('version', $version)
			->set('config', $this->config)
			->set('id', $id)
			->set('contributors', $authors)
			->set('roles', $roles)
			->setErrors($this->getErrors())
			->setLayout('display')
			->display();
	}
}
