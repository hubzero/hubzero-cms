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
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for system config
 */
class SystemControllerLdap extends Hubzero_Controller
{
	/**
	 * Default view
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Set any errors
		if ($this->getError()) 
		{
			foreach ($this->getErrors() as $error) 
			{
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}

	/**
	 * Import the hub configuration
	 * 
	 * @return     void
	 */
	public function importHubconfigTask()
	{
		require_once(JPATH_ROOT . DS . 'hubconfiguration.php');

		$table =& JTable::getInstance('component');
		$table->loadByOption($this->_option);

		$hub_config = new HubConfig();

		$this->config->set('ldap_basedn', $hub_config->hubLDAPBaseDN);
		$this->config->set('ldap_primary', $hub_config->hubLDAPMasterHost);
		$this->config->set('ldap_secondary', $hub_config->hubLDAPSlaveHosts);
		$this->config->set('ldap_tls', $hub_config->hubLDAPNegotiateTLS);
		$this->config->set('ldap_searchdn', $hub_config->hubLDAPSearchUserDN);
		$this->config->set('ldap_searchpw', $hub_config->hubLDAPSearchUserPW);
		$this->config->set('ldap_managerdn', $hub_config->hubLDAPAcctMgrDN);
		$this->config->set('ldap_managerpw', $hub_config->hubLDAPAcctMgrPW);

		$table->params = $this->config->toString();

		$table->store();

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			JText::_('Import completed')
		);
	}

	/**
	 * Delete LDAP group entries
	 *
	 * @return     void
	 */
	public function deleteGroupsTask()
	{
		ximport('Hubzero_Ldap');
		
		Hubzero_Ldap::deleteAllGroups();
	
		$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('All LDAP Group Entries Deleted')
		);
	}
	
	/**
	 * Delete LDAP user entries
	 *
	 * @return     void
	 */
	public function deleteUsersTask()
	{
		ximport('Hubzero_Ldap');
	
		Hubzero_Ldap::deleteAllUsers();
	
		$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('All LDAP User Entries Deleted')
		);
	}
	
	/**
	 * Export all groups to LDAP
	 *
	 * @return     void
	 */
	public function exportGroupsTask()
	{
		ximport('Hubzero_Ldap');
		
		Hubzero_Ldap::syncAllGroups();
	
		$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Groups have been exported to LDAP')
		);
	}
	
	/**
	 * Delete LDAP user entries
	 *
	 * @return     void
	 */
	public function exportUsersTask()
	{
		ximport('Hubzero_Ldap');
	
		Hubzero_Ldap::syncAllUsers();
	
		$this->setRedirect(
				'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
				JText::_('Users have been exported to LDAP')
		);
	}
}
