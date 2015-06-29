<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Components\Installer\Admin\Models;

use Config;
use Lang;
use User;
use App;

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.updater.update');

/**
 * Update model
 */
class Update extends \JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'name',
				'client_id',
				'type',
				'folder',
				'extension_id',
				'update_id',
				'update_site_id',
			);
		}

		parent::__construct($config);
	}

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
	 * Method to get the database query
	 *
	 * @return	JDatabaseQuery	The database query
	 * @since	1.6
	 */
	protected function getListQuery()
	{
		$db    = $this->getDbo();
		$query = $db->getQuery(true);

		// grab updates ignoring new installs
		$query->select('*')->from('#__updates')->where('extension_id != 0');
		$query->order($this->getState('list.ordering') . ' ' . $this->getState('list.direction'));

		// Filter by extension_id
		if ($eid = $this->getState('filter.extension_id'))
		{
			$query->where($db->nq('extension_id') . ' = ' . $db->q((int) $eid));
		}
		else
		{
			$query->where($db->nq('extension_id').' != '.$db->q(0));
			$query->where($db->nq('extension_id').' != '.$db->q(700));
		}

		return $query;
	}

	/**
	 * Finds updates for an extension.
	 *
	 * @param	int		Extension identifier to look for
	 * @return	boolean Result
	 * @since	1.6
	 */
	public function findUpdates($eid=0, $cache_timeout = 0)
	{
		$updater = \JUpdater::getInstance();
		$results = $updater->findUpdates($eid, $cache_timeout);
		return true;
	}

	/**
	 * Removes all of the updates from the table.
	 *
	 * @return	boolean result of operation
	 * @since	1.6
	 */
	public function purge()
	{
		$db = \App::get('db');
		// Note: TRUNCATE is a DDL operation
		// This may or may not mean depending on your database
		$db->setQuery('TRUNCATE TABLE #__updates');
		if ($db->Query())
		{
			// Reset the last update check timestamp
			$query = $db->getQuery(true);
			$query->update($db->nq('#__update_sites'));
			$query->set($db->nq('last_check_timestamp') . ' = ' . $db->q(0));
			$db->setQuery($query);
			$db->query();

			$this->_message = Lang::txt('COM_INSTALLER_PURGED_UPDATES');
			return true;
		}
		else
		{
			$this->_message = Lang::txt('COM_INSTALLER_FAILED_TO_PURGE_UPDATES');
			return false;
		}
	}

	/**
	 * Enables any disabled rows in #__update_sites table
	 *
	 * @return	boolean result of operation
	 * @since	1.6
	 */
	public function enableSites()
	{
		$db = \App::get('db');
		$db->setQuery('UPDATE #__update_sites SET enabled = 1 WHERE enabled = 0');
		if ($db->Query())
		{
			if ($rows = $db->getAffectedRows())
			{
				$this->_message .= Lang::txts('COM_INSTALLER_ENABLED_UPDATES', $rows);
			}
			return true;
		}
		else
		{
			$this->_message .= Lang::txt('COM_INSTALLER_FAILED_TO_ENABLE_UPDATES');
			return false;
		}
	}

	/**
	 * Update function.
	 *
	 * Sets the "result" state with the result of the operation.
	 *
	 * @param	Array[int] List of updates to apply
	 * @since	1.6
	 */
	public function update($uids)
	{
		$result = true;
		foreach ($uids as $uid)
		{
			$update = new \JUpdate();
			$instance = \JTable::getInstance('update');
			$instance->load($uid);
			$update->loadFromXML($instance->detailsurl);
			// install sets state and enqueues messages
			$res = $this->install($update);

			if ($res)
			{
				$instance->delete($uid);
			}

			$result = $res & $result;
		}

		// Set the final state
		$this->setState('result', $result);
	}

	/**
	 * Handles the actual update installation.
	 *
	 * @param	JUpdate	An update definition
	 * @return	boolean	Result of install
	 * @since	1.6
	 */
	private function install($update)
	{
		if (isset($update->get('downloadurl')->_data))
		{
			$url = trim($update->downloadurl->_data);
		}
		else
		{
			App::abort('', Lang::txt('COM_INSTALLER_INVALID_EXTENSION_UPDATE'));
			return false;
		}

		$p_file = JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file)
		{
			App::abort('', Lang::txt('COM_INSTALLER_PACKAGE_DOWNLOAD_FAILED', $url));
			return false;
		}

		$tmp_dest = Config::get('tmp_path');

		// Unpack the downloaded package file
		$package = JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

		// Get an installer instance
		$installer = \JInstaller::getInstance();
		$update->set('type', $package['type']);

		// Install the package
		if (!$installer->update($package['dir']))
		{
			// There was an error updating the package
			$msg = Lang::txt('COM_INSTALLER_MSG_UPDATE_ERROR', Lang::txt('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = false;
		}
		else
		{
			// Package updated successfully
			$msg = Lang::txt('COM_INSTALLER_MSG_UPDATE_SUCCESS', Lang::txt('COM_INSTALLER_TYPE_TYPE_'.strtoupper($package['type'])));
			$result = true;
		}

		// Quick change
		$this->type = $package['type'];

		// Set some model state values
		$app->enqueueMessage($msg);

		// TODO: Reconfigure this code when you have more battery life left
		$this->setState('name', $installer->get('name'));
		$this->setState('result', $result);
		$app->setUserState('com_installer.message', $installer->message);
		$app->setUserState('com_installer.extension_message', $installer->get('extension_message'));

		// Cleanup the install files
		if (!is_file($package['packagefile']))
		{
			$package['packagefile'] = Config::get('tmp_path') . '/' . $package['packagefile'];
		}

		JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

		return $result;
	}
}
