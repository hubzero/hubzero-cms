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

use Hubzero\Config\Registry;
use Exception;
use Component;
use User;
use Lang;

// Import library dependencies
include_once(__DIR__ . DS . 'extension.php');
include_once(dirname(__DIR__) . '/helpers/script.php');

/**
 * Installer Database Model
 */
class Database extends Extension
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
	 *
	 * Fixes database problems
	 */
	public function fix()
	{
		$changeSet = $this->getItems();
		$changeSet->fix();
		$this->fixSchemaVersion($changeSet);
		$this->fixUpdateVersion();
		$installer = new \joomlaInstallerScript();
		$installer->deleteUnexistingFiles();
		$this->fixDefaultTextFilters();
	}

	/**
	 *
	 * Gets the changeset object
	 *
	 * @return  JSchemaChangeset
	 */
	public function getItems()
	{
		$folder = dirname(__DIR__) . '/sql/updates/';
		$changeSet = \JSchemaChangeset::getInstance(\App::get('db'), $folder);
		return $changeSet;
	}

	public function getPagination()
	{
		return true;
	}

	/**
	 * Get version from #__schemas table
	 *
	 * @return  mixed  the return value from the query, or null if the query fails
	 * @throws Exception
	 */

	public function getSchemaVersion()
	{
		$db = \App::get('db');
		$query = $db->getQuery(true);
		$query->select('version_id')->from($db->qn('#__schemas'))
			->where('extension_id = 700');
		$db->setQuery($query);
		$result = $db->loadResult();
		if ($db->getErrorNum())
		{
			throw new Exception('Database error - getSchemaVersion');
		}
		return $result;
	}

	/**
	 * Fix schema version if wrong
	 *
	 * @param JSchemaChangeSet
	 *
	 * @return   mixed  string schema version if success, false if fail
	 */
	public function fixSchemaVersion($changeSet)
	{
		// Get correct schema version -- last file in array
		$schema = $changeSet->getSchema();
		$db = \App::get('db');
		$result = false;

		// Check value. If ok, don't do update
		$version = $this->getSchemaVersion();
		if ($version == $schema)
		{
			$result = $version;
		}
		else
		{
			// Delete old row
			$query = $db->getQuery(true);
			$query->delete($db->qn('#__schemas'));
			$query->where($db->qn('extension_id') . ' = 700');
			$db->setQuery($query);
			$db->query();

			// Add new row
			$query = $db->getQuery(true);
			$query->insert($db->qn('#__schemas'));
			$query->set($db->qn('extension_id') . '= 700');
			$query->set($db->qn('version_id') . '= ' . $db->q($schema));
			$db->setQuery($query);
			if ($db->query())
			{
				$result = $schema;
			}
		}
		return $result;
	}

	/**
	 * Get current version from #__extensions table
	 *
	 * @return  mixed   version if successful, false if fail
	 */

	public function getUpdateVersion()
	{
		$table = \JTable::getInstance('Extension');
		$table->load('700');

		$cache = new Registry($table->manifest_cache);
		return $cache->get('version');
	}

	/**
	 * Fix Joomla version in #__extensions table if wrong (doesn't equal JVersion short version)
	 *
	 * @return   mixed  string update version if success, false if fail
	 */
	public function fixUpdateVersion()
	{
		$table = \JTable::getInstance('Extension');
		$table->load('700');

		$cache = new Registry($table->manifest_cache);
		$updateVersion =  $cache->get('version');

		$cmsVersion = new \JVersion();
		if ($updateVersion == $cmsVersion->getShortVersion())
		{
			return $updateVersion;
		}
		else
		{
			$cache->set('version', $cmsVersion->getShortVersion());
			$table->manifest_cache = $cache->toString();
			if ($table->store())
			{
				return $cmsVersion->getShortVersion();
			}
			else
			{
				return false;
			}
		}
	}

	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank.
	 *
	 * @return  string  default text filters (if any)
	 */
	public function getDefaultTextFilters()
	{
		$table = \JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_config')));
		return $table->params;
	}
	/**
	 * For version 2.5.x only
	 * Check if com_config parameters are blank. If so, populate with com_content text filters.
	 *
	 * @return  mixed  boolean true if params are updated, null otherwise
	 */
	public function fixDefaultTextFilters()
	{
		$table = \JTable::getInstance('Extension');
		$table->load($table->find(array('name' => 'com_config')));

		// Check for empty $config and non-empty content filters
		if (!$table->params)
		{
			// Get filters from com_content and store if you find them
			$contentParams = Component::params('com_content');
			if ($contentParams->get('filters'))
			{
				$newParams = new Registry();
				$newParams->set('filters', $contentParams->get('filters'));
				$table->params = (string) $newParams;
				$table->store();
				return true;
			}
		}
	}
}
