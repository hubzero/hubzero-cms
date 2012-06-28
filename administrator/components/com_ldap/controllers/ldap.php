<?php
/**
 * HUBzero CMS
 *
 * Copyright 2012 Purdue University. All rights reserved.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2012 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

ximport('Hubzero_Controller');

/**
 * Controller class for ldap
 */
class LdapControllerLdap extends Hubzero_Controller
{
	/**
	 * Display a list of entries
	 * 
	 * @return     void
	 */
	public function displayTask()
	{
		// Set any errors
		if ($this->getError()) {
			
			foreach ($this->getErrors() as $error) {
				$this->view->setError($error);
			}
		}

		// Output the HTML
		$this->view->display();
	}
	
	public function importHubconfigTask()
	{
		global $mainframe;
		
		require_once(JPATH_ROOT . '/' . "hubconfiguration.php");

		$params = JComponentHelper::getParams('com_ldap');
		
		$table =& JTable::getInstance('component');
		$table->loadByOption( 'com_ldap' );
			
		$hub_config = new HubConfig();
		
		$params->set('ldap_basedn',$hub_config->hubLDAPBaseDN);
		$params->set('ldap_primary',$hub_config->hubLDAPMasterHost);
		$params->set('ldap_secondary',$hub_config->hubLDAPSlaveHosts);
		$params->set('ldap_tls',$hub_config->hubLDAPNegotiateTLS);
		$params->set('ldap_searchdn',$hub_config->hubLDAPSearchUserDN);
		$params->set('ldap_searchpw',$hub_config->hubLDAPSearchUserPW);
		$params->set('ldap_managerdn',$hub_config->hubLDAPAcctMgrDN);
		$params->set('ldap_managerpw',$hub_config->hubLDAPAcctMgrPW);
		
		$table->params = $params->toString();

		$table->store();
		
		$mainframe->redirect('index.php?option=com_ldap');
	}
}
