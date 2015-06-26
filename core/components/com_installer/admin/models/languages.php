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

use Notify;
use Config;
use Lang;
use App;

// Import library dependencies
jimport('joomla.application.component.modellist');
jimport('joomla.updater.update');

/**
 * Languages Installer Model
 */
class Languages extends \JModelList
{
	/**
	 * Constructor override, defines a white list of column filters.
	 *
	 * @param   array  $config  An optional associative array of configuration settings.
	 *
	 * @see     JModelList
	 */
	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'update_id', 'update_id',
				'name', 'name',
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to get the available languages database query.
	 *
	 * @return	JDatabaseQuery	The database query
	 */
	protected function _getListQuery()
	{
		$db = \App::get('db');
		$query = $db->getQuery(true);

		// Select the required fields from the updates table
		$query->select('update_id, name, version, detailsurl, type');

		$query->from('#__updates');

		// This Where clause will avoid to list languages already installed.
		$query->where('extension_id = 0');

		// Filter by search in title
		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$search = $db->Quote('%' . $db->escape($search, true) . '%');
			$query->where('(name LIKE ' . $search . ')');
		}

		// Add the list ordering clause.
		$listOrder = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');
		$query->order($db->escape($listOrder) . ' ' . $db->escape($orderDirn));

		return $query;
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * @param   string  $id  A prefix for the store id.
	 *
	 * @return  string  A store id.
	 */
	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id	.= ':' . $this->getState('filter.search');

		return parent::getStoreId($id);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @param   null  $ordering   list order
	 * @param   null  $direction  direction in the list
	 *
	 * @return  void
	 */
	protected function populateState($ordering = 'name', $direction = 'asc')
	{
		// Initialise variables.
		$value = Request::getState($this->context . '.filter.search', 'filter_search');
		$this->setState('filter.search', $value);

		$this->setState('extension_message', User::getState('com_installer.extension_message'));

		parent::populateState($ordering, $direction);
	}

	/**
	 * Method to find available languages in the Accredited Languages Update Site.
	 *
	 * @param   int  $cache_timeout  time before refreshing the cached updates
	 * @return  bool
	 */
	public function findLanguages($cache_timeout = 0)
	{
		$updater = \JUpdater::getInstance();

		/*
		 * The following function uses extension_id 600, that is the english language extension id.
		 * In #__update_sites_extensions you should have 600 linked to the Accredited Translations Repo
		 */
		$updater->findUpdates(array(600), $cache_timeout);

		return true;
	}

	/**
	 * Install languages in the system.
	 *
	 * @param   array  $lids  array of language ids selected in the list
	 * @return  bool
	 */
	public function install($lids)
	{
		$installer = \JInstaller::getInstance();

		// Loop through every selected language
		foreach ($lids as $id)
		{
			// Loads the update database object that represents the language
			$language = \JTable::getInstance('update');
			$language->load($id);

			// Get the url to the XML manifest file of the selected language
			$remote_manifest = $this->_getLanguageManifest($id);
			if (!$remote_manifest)
			{
				// Could not find the url, the information in the update server may be corrupt
				$message  = Lang::txt('COM_INSTALLER_MSG_LANGUAGES_CANT_FIND_REMOTE_MANIFEST', $language->name);
				$message .= ' ' . Lang::txt('COM_INSTALLER_MSG_LANGUAGES_TRY_LATER');
				Notify::warning($message);
				continue;
			}

			// Based on the language XML manifest get the url of the package to download
			$package_url = $this->_getPackageUrl($remote_manifest);
			if (!$package_url)
			{
				// Could not find the url , maybe the url is wrong in the update server, or there is not internet access
				$message  = Lang::txt('COM_INSTALLER_MSG_LANGUAGES_CANT_FIND_REMOTE_PACKAGE', $language->name);
				$message .= ' ' . Lang::txt('COM_INSTALLER_MSG_LANGUAGES_TRY_LATER');
				Notify::warning($message);
				continue;
			}

			// Download the package to the tmp folder
			$package = $this->_downloadPackage($package_url);

			// Install the package
			if (!$installer->install($package['dir']))
			{
				// There was an error installing the package
				$message  = Lang::txt('COM_INSTALLER_INSTALL_ERROR', $language->name);
				$message .= ' ' . Lang::txt('COM_INSTALLER_MSG_LANGUAGES_TRY_LATER');
				Notify::warning($message);
				continue;
			}

			// Package installed successfully
			Notify::success(Lang::txt('COM_INSTALLER_INSTALL_SUCCESS', $language->name));

			// Cleanup the install files in tmp folder
			if (!is_file($package['packagefile']))
			{
				$package['packagefile'] = Config::get('tmp_path') . '/' . $package['packagefile'];
			}
			\JInstallerHelper::cleanupInstall($package['packagefile'], $package['extractdir']);

			// Delete the installed language from the list
			$language->delete($id);
		}
	}

	/**
	 * Gets the manifest file of a selected language from a the language list in a update server.
	 *
	 * @param   int  $uid  the id of the language in the #__updates table
	 * @return  string
	 */
	protected function _getLanguageManifest($uid)
	{
		$instance = \JTable::getInstance('update');
		$instance->load($uid);
		$detailurl = trim($instance->detailsurl);
		return $detailurl;
	}

	/**
	 * Finds the url of the package to download.
	 *
	 * @param   string  $remote_manifest  url to the manifest XML file of the remote package
	 * @return  string|bool
	 */
	protected function _getPackageUrl($remote_manifest)
	{
		$update = new \JUpdate;
		$update->loadFromXML($remote_manifest);
		$package_url = trim($update->get('downloadurl', false)->_data);

		return $package_url;
	}

	/**
	 * Download a language package from an URL and unpack it in the tmp folder.
	 *
	 * @param   string  $url  url of the package
	 * @return  array|bool Package details or false on failure
	 */
	protected function _downloadPackage($url)
	{
		// Download the package from the given URL
		$p_file = \JInstallerHelper::downloadPackage($url);

		// Was the package downloaded?
		if (!$p_file)
		{
			App::abort('', Lang::txt('COM_INSTALLER_MSG_INSTALL_INVALID_URL'));
			return false;
		}

		$tmp_dest = Config::get('tmp_path');

		// Unpack the downloaded package file
		$package = \JInstallerHelper::unpack($tmp_dest . '/' . $p_file);

		return $package;
	}
}
