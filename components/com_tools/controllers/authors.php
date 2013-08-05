<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2011 Purdue University. All rights reserved.
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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'tool.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_tools' . DS . 'tables' . DS . 'version.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'resource.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'type.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'assoc.php');
require_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'contributor.php');
require_once(JPATH_ROOT . DS . 'components' . DS . 'com_resources' . DS . 'helpers' . DS . 'helper.php' );

/**
 * Controller class for contributing a tool
 */
class ToolsControllerAuthors extends Hubzero_Controller
{
	/**
	 * Determines task being called and attempts to execute it
	 * 
	 * @return     void
	 */
	public function execute()
	{
		if ($this->juser->get('guest')) 
		{
			// Redirect to home page
			$this->setRedirect(
				$this->config->get('contribtool_redirect', '/home')
			);
			return;
		}

		// Load the com_resources component config
		$rconfig =& JComponentHelper::getParams('com_resources');
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
			$id = JRequest::getInt('pid', 0);
		}
		if (!$id) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			if ($show)
			{
				$this->displayTask($id);
			}
			return;
		}

		ximport('Hubzero_User_Profile');

		// Incoming authors
		$authid = JRequest::getInt('authid', 0, 'post');
		$authorsNewstr = trim(JRequest::getVar('new_authors', '', 'post'));
		$role = JRequest::getVar('role', '', 'post');

		// Turn the string into an array of usernames
		$authorsNew = empty($authorsNew) ? explode(',', $authorsNewstr) : $authorsNew;

		// Instantiate a resource/contributor association object
		$rc = new ResourcesContributor($this->database);
		$rc->subtable = 'resources';
		$rc->subid = $id;

		// Get the last child in the ordering
		$order = $rc->getLastOrder($id, 'resources');
		$order = $order + 1; // new items are always last

		// Was there an ID? (this will come from the author <select>)
		if ($authid) 
		{
			// Check if they're already linked to this resource
			$rc->loadAssociation($authid, $id, 'resources');
			if ($rc->authorid) 
			{
				$this->setError(JText::sprintf('USER_IS_ALREADY_AUTHOR', $authid));
			}
			else 
			{
				// Perform a check to see if they have a contributors page. If not, we'll need to make one
				$xprofile = new Hubzero_User_Profile();
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
					$cid = strtolower($cid);
					// Find the user's account info
					$uid = JUserHelper::getUserId($cid);
					if (!$uid) 
					{
						$this->setError(JText::sprintf('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
						continue;
					}
				}

				$juser =& JUser::getInstance($uid);
				if (!is_object($juser)) 
				{
					$this->setError( JText::sprintf('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
					continue;
				}

				$uid = $juser->get('id');

				if (!$uid) 
				{
					$this->setError(JText::sprintf('COM_CONTRIBUTE_UNABLE_TO_FIND_USER_ACCOUNT', $cid));
					continue;
				}

				// Check if they're already linked to this resource
				$rcc = new ResourcesContributor($this->database);
				$rcc->loadAssociation($uid, $id, 'resources');
				if ($rcc->authorid) 
				{
					$this->setError(JText::sprintf('USER_IS_ALREADY_AUTHOR', $cid));
					continue;
				}

				$this->_authorCheck($uid);

				$xprofile = Hubzero_User_Profile::getInstance($juser->get('id'));
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
		$xprofile = Hubzero_User_Profile::getInstance($id);
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
		$id  = JRequest::getInt('id', 0);
		$pid = JRequest::getInt('pid', 0);

		// Ensure we have a resource ID ($pid) to work with
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Ensure we have the contributor's ID ($id)
		if ($id) 
		{
			$rc = new ResourcesContributor($this->database);
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
		$ids = JRequest::getVar('authors', array(), 'post');
		$pid = JRequest::getInt('pid', 0);

		// Ensure we have a resource ID ($pid) to work with
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_COM_CONTRIBUTE_NO_ID'));
			$this->displayTask();
			return;
		}

		// Ensure we have the contributor's ID ($id)
		if ($ids) 
		{
			foreach ($ids as $id => $data)
			{
				$rc = new ResourcesContributor($this->database);
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
		$id   = JRequest::getInt('id', 0);
		$pid  = JRequest::getInt('pid', 0);
		$move = 'order' . JRequest::getVar('move', 'down');

		// Ensure we have an ID to work with
		if (!$id) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_CHILD_ID'));
			$this->displayTask($pid);
			return;
		}

		// Ensure we have a parent ID to work with
		if (!$pid) 
		{
			$this->setError(JText::_('COM_TOOLS_CONTRIBUTE_NO_ID'));
			$this->displayTask($pid);
			return;
		}

		// Get the element moving down - item 1
		$author1 = new ResourcesContributor($this->database);
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
			$id = JRequest::getInt('rid', 0);
		}

		$this->view->version = JRequest::getVar('version', 'dev');

		// Ensure we have an ID to work with
		if (!$id) 
		{
			JError::raiseError(500, JText::_('COM_TOOLS_No resource ID found'));
			return;
		}

		// Get all contributors of this resource
		$helper = new ResourcesHelper($id, $this->database);
		if ($this->view->version == 'dev') 
		{
			$helper->getCons();
		}
		else 
		{
			$obj = new Tool($this->database);
			$toolname = $obj->getToolnameFromResource($id);

			$objV = new ToolVersion($this->database);
			$revision = $objV->getCurrentVersionProperty($toolname, 'revision');

			$helper->getToolAuthors($toolname, $revision);
		}

		// Get a list of all existing contributors
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'profile.php');
		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_members' . DS . 'tables' . DS . 'association.php');

		include_once(JPATH_ROOT . DS . 'administrator' . DS . 'components' . DS . 'com_resources' . DS . 'tables' . DS . 'role.type.php');

		$resource = new ResourcesResource($this->database);
		$resource->load($id);

		$rt = new ResourcesContributorRoleType($this->database);

		// Output HTML
		$this->view->config = $this->config;
		$this->view->contributors = $helper->_contributors;
		$this->view->id = $id;

		$this->view->roles = $rt->getRolesForType($resource->type);

		$this->_getStyles($this->_option, 'assets/css/component.css');

		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error)
			{
				$this->view->setError($error);
			}
		}

		$this->view->display();
	}
}
