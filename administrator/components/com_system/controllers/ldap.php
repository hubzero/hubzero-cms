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

/**
 * Controller class for system config
 */
class SystemControllerLdap extends \Hubzero\Component\AdminController
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
		if (file_exists(JPATH_ROOT . DS . 'hubconfiguration.php'))
		{
			include_once(JPATH_ROOT . DS . 'hubconfiguration.php');
		}

		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$table = JTable::getInstance('component');
			$table->loadByOption($this->_option);
		}
		else
		{
			$table = new JTableExtension($this->database);
			$table->load($table->find(array('element' => $this->_option, 'type' => 'component')));
		}

		if (class_exists('HubConfig'))
		{
			$hub_config = new HubConfig();
	
			$this->config->set('ldap_basedn', $hub_config->hubLDAPBaseDN);
			$this->config->set('ldap_primary', $hub_config->hubLDAPMasterHost);
			$this->config->set('ldap_secondary', $hub_config->hubLDAPSlaveHosts);
			$this->config->set('ldap_tls', $hub_config->hubLDAPNegotiateTLS);
			$this->config->set('ldap_searchdn', $hub_config->hubLDAPSearchUserDN);
			$this->config->set('ldap_searchpw', $hub_config->hubLDAPSearchUserPW);
			$this->config->set('ldap_managerdn', $hub_config->hubLDAPAcctMgrDN);
			$this->config->set('ldap_managerpw', $hub_config->hubLDAPAcctMgrPW);
		}

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
		$result = \Hubzero\Utility\Ldap::deleteAllGroups();

		$messageType = 'info';
		$message     = 'We are unable to decisivly say the result of the previous request';

		if(isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			$messageType = 'error';
			$message     = JText::_('LDAP export failed: ' . $result['fatal'][0]);
		}
		elseif(isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			$messageType = 'warning';
			$message     = JText::_('The operation completed, but ' . count($result['warning']) . ' warning(s) occured');
		}
		elseif(isset($result['success']))
		{
			$messageType = 'passed';
			$message     = JText::_("All ({$result['deleted']}) LDAP Group Entries Deleted");
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$messageType
		);
	}

	/**
	 * Delete LDAP user entries
	 *
	 * @return     void
	 */
	public function deleteUsersTask()
	{
		$result = \Hubzero\Utility\Ldap::deleteAllUsers();

		$messageType = 'info';
		$message     = 'We are unable to decisivly say the result of the previous request';

		if(isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			$messageType = 'error';
			$message     = JText::_('LDAP export failed: ' . $result['fatal'][0]);
		}
		elseif(isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			$messageType = 'warning';
			$message     = JText::_('The operation completed, but ' . count($result['warning']) . ' warning(s) occured');
		}
		elseif(isset($result['success']))
		{
			$messageType = 'passed';
			$message     = JText::_("All ({$result['deleted']}) LDAP User Entries Deleted");
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$messageType
		);
	}

	/**
	 * Export all groups to LDAP
	 *
	 * @return     void
	 */
	public function exportGroupsTask()
	{
		$result = \Hubzero\Utility\Ldap::syncAllGroups();

		$messageType = 'info';
		$message     = 'We are unable to decisivly say the result of the previous request';

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			$messageType = 'error';
			$message     = JText::_('LDAP export failed: ' . $result['fatal'][0]);
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			$messageType = 'warning';
			$message     = JText::_('The operation completed, but ' . count($result['warning']) . ' warning(s) occured');
		}
		elseif (isset($result['success']))
		{
			$messageType = 'passed';
			$message     = JText::_("Groups have been exported to LDAP ({$result['added']} added, {$result['modified']} modified, {$result['deleted']} deleted and {$result['unchanged']} unchanged)");
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$messageType
		);
	}

	/**
	 * Delete LDAP user entries
	 *
	 * @return     void
	 */
	public function exportUsersTask()
	{
		$result = \Hubzero\Utility\Ldap::syncAllUsers();

		$messageType = 'info';
		$message     = 'We are unable to decisivly say the result of the previous request';

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			$messageType = 'error';
			$message     = JText::_('LDAP export failed: ' . $result['fatal'][0]);
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			$messageType = 'warning';
			$message     = JText::_('The operation completed, but ' . count($result['warning']) . ' warning(s) occured');
		}
		elseif (isset($result['success']))
		{
			$messageType = 'passed';
			$message     = JText::_("Users have been exported to LDAP ({$result['added']} added, {$result['modified']} modified, {$result['deleted']} deleted and {$result['unchanged']} unchanged)");
		}

		$this->setRedirect(
			'index.php?option=' . $this->_option . '&controller=' . $this->_controller,
			$message,
			$messageType
		);
	}
}
