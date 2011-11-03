<?php
/**
 * @package	 hubzero-cms
 * @author	  Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license	 http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

class GroupsControllerSystem extends Hubzero_Controller
{
	public function displayTask()
	{
		// Get configuration
		$app =& JFactory::getApplication();
		$config = JFactory::getConfig();

		$xhub = &Hubzero_Factory::getHub();

		$this->view->ldapBaseDN = $xhub->getCfg('hubLDAPBaseDN');
		$this->view->ldapURI = $xhub->getCfg('hubLDAPMasterHost');
		$this->view->ldapTLS = $xhub->getCfg('hubLDAPNegotiateTLS');
		$this->view->ldapSearchUserDN = $xhub->getCfg('hubLDAPSearchUserDN');
		$this->view->ldapSearchUserPW = $xhub->getCfg('hubLDAPSearchUserPW');
		$this->view->ldapAcctMgrDN = $xhub->getCfg('hubLDAPAcctMgrDN');
		$this->view->ldapAcctMgrPW = $xhub->getCfg('hubLDAPAcctMgrPW');

		$this->view->conn = &Hubzero_Factory::getPLDC();

		$this->view->status = Hubzero_Group::status();

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	public function exporttoldapTask()
	{
		// Instantiate a new view
		$this->view->post = (JRequest::getMethod() == 'POST');

		if ($this->view->post)
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');

			// Authorization check
			// @TODO: Should probably limit this to super admins

			// Validate inputs

			$replace = JRequest::getVar('replace', null, 'post', 'int');

			if ($replace != 0 && $replace != 1)
			{
				jexit('Invalid POST Data');
			}

			$update = JRequest::getVar('update', null, 'post', 'int');

			if ($update != 0 && $update != 1)
			{
				jexit('Invalid POST Data');
			}

			$objectclass = JRequest::getVar('objectclass', null, 'post', 'word');

			if ($objectclass == 'posixgroup')
			{
				$legacy = false;
			}
			else if ($objectclass == 'hubgroup')
			{
				$legacy = true;
			}
			else
			{
				jexit('Invalid POST Data');
			}

			$extended = JRequest::getVar('extended', null, 'post', 'int');

			if ($extended != 0 && $extended != 1)
			{
				jexit('Invalid POST Data');
			}

			$verbose = JRequest::getVar('verbose', null, 'post', 'int');

			if ($verbose != 0 && $verbose != 1)
			{
				jexit('Invalid POST Data');
			}

			$dryrun = JRequest::getVar('dryrun', null, 'post', 'int');

			if ($dryrun != 0 && $dryrun != 1)
			{
				jexit('Invalid POST Data');
			}

			// Execute action

			Hubzero_Group::exportSQLtoLDAP($extended, $replace, $update, $legacy, $verbose, $dryrun);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}

	public function importldapTask()
	{
		$this->view->post = (JRequest::getMethod() == 'POST');

		if ($this->view->post)
		{
			// Check for request forgeries
			JRequest::checkToken() or jexit('Invalid Token');

			// Authorization check
			// @TODO: Should probably limit this to super admins

			// Validate inputs

			$replace = JRequest::getVar('replace', null, 'post', 'int');

			if ($replace != 0 && $replace != 1)
			{
				jexit('Invalid POST Data');
			}

			$update = JRequest::getVar('update', null, 'post', 'int');

			if ($update != 0 && $update != 1)
			{
				jexit('Invalid POST Data');
			}

			$objectclass = JRequest::getVar('objectclass', null, 'post', 'word');

			if ($objectclass == 'posixgroup')
			{
				$legacy = false;
			}
			else if ($objectclass == 'hubgroup')
			{
				$legacy = true;
			}
			else
			{
				jexit('Invalid POST Data');
			}

			$extended = JRequest::getVar('extended', null, 'post', 'int');

			if ($extended != 0 && $extended != 1)
			{
				jexit('Invalid POST Data');
			}

			// Execute action
			Hubzero_Group::importSQLfromLDAP($extended, $replace, $update, $legacy, true /* verbose */, false /* dryrun */);
		}

		// Set any errors
		if ($this->getError())
		{
			$this->view->setError($this->getError());
		}

		// Output the HTML
		$this->view->display();
	}
}

