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

namespace Components\System\Admin\Controllers;

use Hubzero\Component\AdminController;
use Route;
use Lang;
use App;

/**
 * Controller class for system config
 */
class Ldap extends AdminController
{
	/**
	 * Default view
	 *
	 * @return  void
	 */
	public function displayTask()
	{
		// Output the HTML
		$this->view->display();
	}

	/**
	 * Import the hub configuration
	 *
	 * @return  void
	 */
	public function importHubconfigTask()
	{
		if (file_exists(PATH_APP . DS . 'hubconfiguration.php'))
		{
			include_once PATH_APP . DS . 'hubconfiguration.php';
		}

		if (class_exists('HubConfig'))
		{
			$hub_config = new \HubConfig();

			$this->config->set('ldap_basedn', $hub_config->hubLDAPBaseDN);
			$this->config->set('ldap_primary', $hub_config->hubLDAPMasterHost);
			$this->config->set('ldap_secondary', $hub_config->hubLDAPSlaveHosts);
			$this->config->set('ldap_tls', $hub_config->hubLDAPNegotiateTLS);
			$this->config->set('ldap_searchdn', $hub_config->hubLDAPSearchUserDN);
			$this->config->set('ldap_searchpw', $hub_config->hubLDAPSearchUserPW);
			$this->config->set('ldap_managerdn', $hub_config->hubLDAPAcctMgrDN);
			$this->config->set('ldap_managerpw', $hub_config->hubLDAPAcctMgrPW);
		}

		$db = App::get('db');

		$query = $db->getQuery()
			->update('#__extensions')
			->set(array(
				'params' => $this->config->toString()
			))
			->whereEquals('element', $this->_option)
			->whereEquals('type', 'component');

		$db->setQuery($query->toString());
		$db->query();

		Notify::success(Lang::txt('COM_SYSTEM_LDAP_IMPORT_COMPLETE'));

		$this->cancelTask();
	}

	/**
	 * Delete LDAP group entries
	 *
	 * @return  void
	 */
	public function deleteGroupsTask()
	{
		$result = \Hubzero\Utility\Ldap::deleteAllGroups();

		//Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
		}
		elseif (isset($result['success']))
		{
			Notify::info(Lang::txt('COM_SYSTEM_LDAP_GROUP_ENTRIES_DELETED', $result['deleted']));
		}

		App::redirect(
			Route::url('index.php?option=' . $this->_option . '&controller=' . $this->_controller, false)
		);
	}

	/**
	 * Delete LDAP user entries
	 *
	 * @return  void
	 */
	public function deleteUsersTask()
	{
		$result = \Hubzero\Utility\Ldap::deleteAllUsers();

		//Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
		}
		elseif (isset($result['success']))
		{
			Notify::info(Lang::txt('COM_SYSTEM_LDAP_USER_ENTRIES_DELETED', $result['deleted']));
		}

		$this->cancelTask();
	}

	/**
	 * Export all groups to LDAP
	 *
	 * @return  void
	 */
	public function exportGroupsTask()
	{
		$result = \Hubzero\Utility\Ldap::syncAllGroups();

		//Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
		}
		elseif (isset($result['success']))
		{
			Notify::info(Lang::txt('COM_SYSTEM_LDAP_GROUPS_EXPORTED', $result['added'], $result['modified'], $result['deleted'], $result['unchanged']));
		}

		$this->cancelTask();
	}

	/**
	 * Delete LDAP user entries
	 *
	 * @return  void
	 */
	public function exportUsersTask()
	{
		$result = \Hubzero\Utility\Ldap::syncAllUsers();

		//Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_RESULT_UNKNOWN'));

		if (isset($result['errors']) && isset($result['fatal']) && !empty($result['fatal'][0]))
		{
			Notify::error(Lang::txt('COM_SYSTEM_LDAP_ERROR_EXPORT_FAILED', $result['fatal'][0]));
		}
		elseif (isset($result['errors']) && isset($result['warning']) && !empty($result['warning'][0]))
		{
			Notify::warning(Lang::txt('COM_SYSTEM_LDAP_WARNING_COMPLETED_WITH_ERRORS', count($result['warning'])));
		}
		elseif (isset($result['success']))
		{
			Notify::info(Lang::txt('COM_SYSTEM_LDAP_USERS_EXPORTED', $result['added'], $result['modified'], $result['deleted'], $result['unchanged']));
		}

		$this->cancelTask();
	}
}
