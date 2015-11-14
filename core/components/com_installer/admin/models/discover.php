<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @copyright Copyright 2005-2014 Open Source Matters, Inc.
 * @license   http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 */

namespace Components\Installer\Admin\Models;

use Request;
use Notify;
use Lang;
use User;

// Import library dependencies
require_once __DIR__ . DS . 'extension.php';

/**
 * Installer Discover Model
 */
class Discover extends Extension
{
	protected $_context = 'com_installer.discover';

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		$this->setState('message', User::getState('com_installer.message'));
		$this->setState('extension_message', User::getState('com_installer.extension_message'));

		User::setState('com_installer.message', '');
		User::setState('com_installer.extension_message', '');

		parent::populateState('name', 'asc');
	}

	/**
	 * Method to get the database query.
	 *
	 * @return	JDatabaseQuery the database query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$db = \App::get('db');
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from('#__extensions');
		$query->where('state=-1');
		return $query;
	}

	/**
	 * Discover extensions.
	 *
	 * Finds uninstalled extensions
	 *
	 * @since	1.6
	 */
	public function discover()
	{
		$installer = \JInstaller::getInstance();
		$results   = $installer->discover();

		// Get all templates, including discovered ones
		$query = 'SELECT extension_id, element, folder, client_id, type FROM `#__extensions`';
		$dbo = \App::get('db');
		$dbo->setQuery($query);
		$installedtmp = $dbo->loadObjectList();
		$extensions = array();

		foreach ($installedtmp as $install)
		{
			$key = implode(':', array($install->type, $install->element, $install->folder, $install->client_id));
			$extensions[$key] = $install;
		}

		unset($installedtmp);

		foreach ($results as $result)
		{
			// check if we have a match on the element
			$key = implode(':', array($result->type, $result->element, $result->folder, $result->client_id));
			if (!array_key_exists($key, $extensions))
			{
				$result->store(); // put it into the table
			}
		}
	}

	/**
	 * Installs a discovered extension.
	 *
	 * @since	1.6
	 */
	public function discover_install()
	{
		$installer = \JInstaller::getInstance();
		$eid = Request::getVar('cid', 0);

		if (is_array($eid) || $eid)
		{
			if (!is_array($eid))
			{
				$eid = array($eid);
			}
			\Hubzero\Utility\Arr::toInteger($eid);

			$failed = false;
			foreach ($eid as $id)
			{
				$result = $installer->discover_install($id);
				if (!$result)
				{
					$failed = true;
					Notify::error(Lang::txt('COM_INSTALLER_MSG_DISCOVER_INSTALLFAILED').': '. $id);
				}
			}
			$this->setState('action', 'remove');
			$this->setState('name', $installer->get('name'));

			User::setState('com_installer.message', $installer->message);
			User::setState('com_installer.extension_message', $installer->get('extension_message'));

			if (!$failed)
			{
				Notify::success(Lang::txt('COM_INSTALLER_MSG_DISCOVER_INSTALLSUCCESSFUL'));
			}
		}
		else
		{
			Notify::warning(Lang::txt('COM_INSTALLER_MSG_DISCOVER_NOEXTENSIONSELECTED'));
		}
	}

	/**
	 * Cleans out the list of discovered extensions.
	 *
	 * @since	1.6
	 */
	public function purge()
	{
		$db = \App::get('db');
		$query = $db->getQuery(true);
		$query->delete();
		$query->from('#__extensions');
		$query->where('state = -1');
		$db->setQuery((string)$query);
		if ($db->Query())
		{
			$this->_message = Lang::txt('COM_INSTALLER_MSG_DISCOVER_PURGEDDISCOVEREDEXTENSIONS');
			return true;
		}
		else
		{
			$this->_message = Lang::txt('COM_INSTALLER_MSG_DISCOVER_FAILEDTOPURGEEXTENSIONS');
			return false;
		}
	}
}
