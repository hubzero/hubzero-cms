<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2013 Purdue University. All rights reserved.
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
 * @copyright Copyright 2005-2013 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Content\Migration;

use Hubzero\Config\Processor\Ini;

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Base migration class
 **/
class Base
{
	/**
	 * Database object
	 *
	 * @var object
	 **/
	protected $db;

	/**
	 * Available callbacks
	 *
	 * @var object
	 **/
	protected $callbacks = array();

	/**
	 * Constructor
	 *
	 * @return void
	 **/
	public function __construct($db, $callbacks=array())
	{
		$this->db        = $db;
		$this->callbacks = $callbacks;
	}

	/**
	 * Helper function for calling a given callback
	 *
	 * @param (string) $callback - name of callback to use
	 * @param (string) $fund     - name of callback function to call
	 * @param (array)  $args     - args to pass to callback function
	 * @return void
	 **/
	public function callback($callback, $func, $args=array())
	{
		// Make sure the callback is set (this is protecting us when running in non-interactive mode and callbacks aren't set)
		if (!isset($this->callbacks[$callback]))
		{
			return false;
		}

		// Call function
		call_user_func_array(array($this->callbacks[$callback], $func), $args);
	}

	/**
	 * Try to get the root credentials from a variety of locations
	 *
	 * @return (mixed) $return - array of creds or false on failure
	 **/
	private function getRootCredentials()
	{
		$secrets   = DS . 'etc'  . DS . 'hubzero.secrets';
		$conf_file = DS . 'root' . DS . '.my.cnf';
		$hub_maint = DS . 'etc'  . DS . 'mysql' . DS . 'hubmaint.cnf';

		if (file_exists($secrets))
		{
			$conf = Ini::parse($secrets);
			$user = 'root';
			$pw   = $conf['MYSQL-ROOT'];

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (file_exists($conf_file))
		{
			$conf = Ini::parse($conf_file, true);
			$user = $conf['client']['user'];
			$pw   = $conf['client']['password'];

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($hub_maint) && is_readable($hub_maint))
		{
			$conf = Ini::parse($hub_maint, true);
			$user = $conf['client']['user'];
			$pw   = $conf['client']['password'];

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		return false;
	}

	/**
	 * Try to run commands as MySql root user
	 *
	 * @return (bool) $success - if successfully upgraded to root access
	 **/
	public function runAsRoot()
	{
		if ($creds = $this->getRootCredentials())
		{
			// Instantiate a config object
			$jconfig = new \JConfig();

			$db = \JDatabase::getInstance(
				array(
					'driver'   => 'pdo',
					'host'     => $jconfig->host,
					'user'     => $creds['user'],
					'password' => $creds['password'],
					'database' => $jconfig->db,
					'prefix'   => 'jos_'
				)
			);

			// Test the connection
			if (!$db->connected())
			{
				return false;
			}
			else
			{
				$this->db = $db;
				return true;
			}
		}

		return false;
	}

	/**
	 * Add, as needed, the component to the appropriate table, depending on the Joomla version
	 *
	 * @param $name           - (string) component name
	 * @param $option         - (string) com_xyz
	 * @param $enabled        - (int)    whether or not the component should be enabled
	 * @param $params         - (string) component params (if already known)
	 * @param $createMenuItem - (bool)   create an admin menu item for this component
	 * @return bool
	 **/
	public function addComponentEntry($name, $option=NULL, $enabled=1, $params='', $createMenuItem=true)
	{
		if ($this->db->tableExists('#__components'))
		{
			// First, make sure it isn't already there
			$query = "SELECT `id` FROM `#__components` WHERE `name` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				return true;
			}

			if (is_null($option))
			{
				$option = 'com_' . strtolower($name);
			}

			$ordering = 0;

			if (!empty($params) && is_array($params))
			{
				$p = '';
				foreach ($params as $k => $v)
				{
					$p .= "{$k}={$v}\n";
				}

				$params = $p;
			}

			$query = "INSERT INTO `#__components` (`name`, `link`, `menuid`, `parent`, `admin_menu_link`, `admin_menu_alt`, `option`, `ordering`, `admin_menu_img`, `iscore`, `params`, `enabled`)";
			$query .= " VALUES ('{$name}', 'option={$option}', 0, 0, 'option={$option}', '{$name}', '{$option}', {$ordering}, '', 0, ".$this->db->quote($params).", {$enabled})";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			if (is_null($option))
			{
				$option = 'com_' . strtolower($name);
			}
			$name = $option;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = " . $this->db->quote($option);
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				$component_id = $this->db->loadResult();
			}
			else
			{
				$ordering = 0;

				if (!empty($params) && is_array($params))
				{
					$params = json_encode($params);
				}

				$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
				$query .= " VALUES ('{$name}', 'component', '{$option}', '', 1, {$enabled}, 1, 0, '', ".$this->db->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
				$this->db->setQuery($query);
				$this->db->query();
				$component_id = $this->db->insertId();
			}

			// Secondly, add asset entry if not yet created
			$query = "SELECT `id` FROM `#__assets` WHERE `name` = " . $this->db->quote($option);
			$this->db->setQuery($query);
			if (!$this->db->loadResult())
			{
				// Build default ruleset
				$defaulRules = array(
					"core.admin"      => array(
						"7" => 1
						),
					"core.manage"     => array(
						"6" => 1
						),
					"core.create"     => array(),
					"core.delete"     => array(),
					"core.edit"       => array(),
					"core.edit.state" => array()
					);

				// Register the component container just under root in the assets table
				$asset = \JTable::getInstance('Asset');
				$asset->name = $option;
				$asset->parent_id = 1;
				$asset->rules = json_encode($defaulRules);
				$asset->title = $option;
				$asset->setLocation(1, 'last-child');
				$asset->store();
			}

			if ($createMenuItem)
			{
				// Check for an admin menu entry...if it's not there, create it
				$query = "SELECT `id` FROM `#__menu` WHERE `menutype` = 'main' AND `title` = " . $this->db->quote($option);
				$this->db->setQuery($query);
				if ($this->db->loadResult())
				{
					return true;
				}

				$alias = substr($option, 4);

				$query = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)";
				$query .= " VALUES ('main', '{$option}', '{$alias}', '', '{$alias}', 'index.php?option={$option}', 'component', {$enabled}, 1, 1, {$component_id}, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 0, 0, '*', 1)";
				$this->db->setQuery($query);
				$this->db->query();

				// If we have the nested set class available, use it to rebuild lft/rgt
				if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
				{
					// Use the MySQL driver for this
					$config = \JFactory::getConfig();
					$database = \JDatabase::getInstance(
						array(
							'driver'   => 'mysql',
							'host'     => $config->getValue('host'),
							'user'     => $config->getValue('user'),
							'password' => $config->getValue('password'),
							'database' => $config->getValue('db')
						) 
					);

					$table = new \JTableMenu($database);
					$table->rebuild();
				}
			}
		}
	}

	/**
	 * Add, as needed, the plugin entry to the appropriate table, depending on the Joomla version
	 *
	 * @param $folder  - (string) plugin folder
	 * @param $element - (string) plugin element
	 * @param $enabled - (int)    whether or not the plugin should be enabled
	 * @param $params  - (array)  plugin params (if already known)
	 * @return bool
	 **/
	public function addPluginEntry($folder, $element, $enabled=1, $params='')
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = ucfirst($folder) . ' - ' . ucfirst($element);

			// First, make sure it isn't already there
			$query = "SELECT `id` FROM `#__plugins` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				return true;
			}

			// Get ordering
			$query = "SELECT MAX(ordering) FROM `#__plugins` WHERE `folder` = " . $this->db->quote($folder);
			$this->db->setQuery($query);
			$ordering = (is_numeric($this->db->loadResult())) ? $this->db->loadResult()+1 : 1;

			if (!empty($params) && is_array($params))
			{
				$p = '';
				foreach ($params as $k => $v)
				{
					$p .= "{$k}={$v}\n";
				}

				$params = $p;
			}

			$query = "INSERT INTO `#__plugins` (`name`, `element`, `folder`, `access`, `ordering`, `published`, `iscore`, `client_id`, `checked_out`, `checked_out_time`, `params`)";
			$query .= " VALUES ('{$name}', '{$element}', '{$folder}', 0, {$ordering}, {$enabled}, 0, 0, 0, '0000-00-00 00:00:00', ".$this->db->quote($params).")";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = 'plg_' . $folder . '_' . $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				return true;
			}

			// Get ordering
			$query = "SELECT MAX(ordering) FROM `#__extensions` WHERE `folder` = " . $this->db->quote($folder);
			$this->db->setQuery($query);
			$ordering = (is_numeric($this->db->loadResult())) ? $this->db->loadResult()+1 : 1;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
			$query .= " VALUES ('{$name}', 'plugin', '{$element}', '{$folder}', 0, {$enabled}, 1, 0, '', ".$this->db->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Add, as needed, the module entry to the appropriate table, depending on the Joomla version
	 *
	 * @param $element - (string) plugin element
	 * @param $enabled - (int)    whether or not the plugin should be enabled
	 * @param $params  - (array)  plugin params (if already known)
	 * @return bool
	 **/
	public function addModuleEntry($element, $enabled=1, $params='')
	{
		if ($this->db->tableExists('#__extensions'))
		{
			$name = $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			if ($this->db->loadResult())
			{
				return true;
			}

			$ordering = 0;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
			$query .= " VALUES ('{$name}', 'module', '{$element}', '', 0, {$enabled}, 1, 0, '', ".$this->db->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Remove component entries from the appropriate table, depending on the Joomla version
	 *
	 * @param $name - (string) component name
	 * @return bool
	 **/
	public function deleteComponentEntry($name)
	{
		if ($this->db->tableExists('#__components'))
		{
			// Delete component entry
			$query = "DELETE FROM `#__components` WHERE `name` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$name = 'com_' . strtolower($name);
			// Delete component entry
			$query = "DELETE FROM `#__extensions` WHERE `name` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			$this->db->query();

			// Remove the component container in the assets table
			$asset = \JTable::getInstance('Asset');
			if ($asset->loadByName($name))
			{
				$asset->delete();
			}

			// Check for an admin menu entry...if it's not there, create it
			$query = "DELETE FROM `#__menu` WHERE `menutype` = 'main' AND `title` = " . $this->db->quote($name);
			$this->db->setQuery($query);
			$this->db->query();

			// If we have the nested set class available, use it to rebuild lft/rgt
			if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
			{
				// Use the MySQL driver for this
				$config = \JFactory::getConfig();
				$database = \JDatabase::getInstance(
					array(
						'driver'   => 'mysql',
						'host'     => $config->getValue('host'),
						'user'     => $config->getValue('user'),
						'password' => $config->getValue('password'),
						'database' => $config->getValue('db')
					) 
				);

				$table = new \JTableMenu($database);
				$table->rebuild();
			}
		}
	}

	/**
	 * Remove plugin entries from the appropriate table, depending on the Joomla version
	 *
	 * @param $name - (string) plugin name
	 * @return bool
	 **/
	public function deletePluginEntry($folder, $element=NULL)
	{
		if ($this->db->tableExists('#__plugins'))
		{
			// Delete plugin(s) entry
			$query = "DELETE FROM `#__plugins` WHERE `folder` = " . $this->db->quote($folder) . ((!is_null($element)) ? " AND `element` = '{$element}'" : "");
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			// Delete plugin(s) entry
			$query = "DELETE FROM `#__extensions` WHERE `folder` = " . $this->db->quote($folder) . ((!is_null($element)) ? " AND `element` = '{$element}'" : "");
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Remove module entries from the appropriate table, depending on the Joomla version
	 *
	 * @param $name - (string) plugin name
	 * @return bool
	 **/
	public function deleteModuleEntry($element)
	{
		if ($this->db->tableExists('#__extensions'))
		{
			// Delete module entry
			$query = "DELETE FROM `#__extensions` WHERE `element` = '{$element}'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}

	/**
	 * Enable plugin
	 *
	 * @param  $folder  - (string) plugin folder
	 * @param  $element - (string) plugin element
	 * @return void
	 **/
	public function enablePlugin($folder, $element)
	{
		$this->setPluginStatus($folder, $element, 1);
	}

	/**
	 * Disable plugin
	 *
	 * @param  $folder  - (string) plugin folder
	 * @param  $element - (string) plugin element
	 * @return void
	 **/
	public function disablePlugin($folder, $element)
	{
		$this->setPluginStatus($folder, $element, 0);
	}

	/**
	 * Enable/disable plugin
	 *
	 * @param  $folder  - (string) plugin folder
	 * @param  $element - (string) plugin element
	 * @param  $enabled - (int)    whether or not the plugin should be enabled
	 * @return void
	 **/
	private function setPluginStatus($folder, $element, $enabled=1)
	{
		if ($this->db->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `published` = '{$enabled}' WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->db->setQuery($query);
			$this->db->query();
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `enabled` = '{$enabled}' WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->db->setQuery($query);
			$this->db->query();
		}
	}
}