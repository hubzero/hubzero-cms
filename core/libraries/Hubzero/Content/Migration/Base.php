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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration;

use Hubzero\Config\Registry;
use Hubzero\Config\Processor;
use Hubzero\Database\Driver;

/**
 * Base migration class
 **/
class Base
{
	/**
	 * Base database object (should have joomla extensions and migrations log tables in it)
	 *
	 * @var  object
	 **/
	private $baseDb;

	/**
	 * Db object available to migrations
	 *
	 * @var  string
	 **/
	protected $db;

	/**
	 * Available callbacks
	 *
	 * @var  object
	 **/
	protected $callbacks = array();

	/**
	 * Options
	 *
	 * @var  array
	 **/
	protected $options = array();

	/**
	 * Errors
	 *
	 * @var  array
	 **/
	protected $errors = array();

	/**
	 * Whether or not we're running in protected mode
	 *
	 * @var  bool
	 **/
	private $protectedMode = true;

	/**
	 * Constructor
	 *
	 * @param   object  $db         Database object (primary)
	 * @param   array   $callbacks  Callbacks
	 * @param   object  $altDb      Alternate db
	 * @return  void
	 **/
	public function __construct($db, $callbacks=array(), $altDb=null)
	{
		$this->baseDb    = $db;
		$this->db        = (isset($altDb)) ? $altDb : $db;
		$this->callbacks = $callbacks;

		if (!isset($altDb))
		{
			$this->protectedMode = false;
		}
	}

	/**
	 * Helper function for calling a given callback
	 *
	 * @param   string  $callback  Name of callback to use
	 * @param   string  $fund      Name of callback function to call
	 * @param   array   $args      Args to pass to callback function
	 * @return  mixed
	 **/
	public function callback($callback, $func, $args=array())
	{
		// Make sure the callback is set (this is protecting us when running in non-interactive mode and callbacks aren't set)
		if (!isset($this->callbacks[$callback]))
		{
			return false;
		}

		// Call function
		return call_user_func_array(array($this->callbacks[$callback], $func), $args);
	}

	/**
	 * Get option - these are specified/overwritten by the individual migrations/hooks
	 *
	 * @param   string  $key
	 * @return  mixed
	 **/
	public function getOption($key)
	{
		return (isset($this->options[$key])) ? $this->options[$key] : false;
	}

	/**
	 * Return a middleware database object
	 *
	 * @return  object
	 */
	public function getMWDBO()
	{
		static $instance;

		if (!is_object($instance))
		{
			$config = $this->getParams('com_tools');

			$options['driver']   = 'pdo';
			$options['host']     = $config->get('mwDBHost');
			$options['port']     = $config->get('mwDBPort');
			$options['user']     = $config->get('mwDBUsername');
			$options['password'] = $config->get('mwDBPassword');
			$options['database'] = $config->get('mwDBDatabase');
			$options['prefix']   = $config->get('mwDBPrefix');

			if ((!isset($options['password']) || $options['password'] == '')
			 && (!isset($options['user'])     || $options['user'] == '')
			 && (!isset($options['database']) || $options['database'] == ''))
			{
				$instance = $this->db;
			}
			else
			{
				try
				{
					$instance = \JDatabase::getInstance($options);
				}
				catch (\PDOException $e)
				{
					$instance = NULL;
					return false;
				}
			}

			// Test the connection
			if (!$instance->connected())
			{
				$instance = NULL;
				return false;
			}
		}

		return $instance;
	}

	/**
	 * Try to get the root credentials from a variety of locations
	 *
	 * @return  mixed  Array of creds or false on failure
	 **/
	private function getRootCredentials()
	{
		$secrets   = DS . 'etc'  . DS . 'hubzero.secrets';
		$conf_file = DS . 'root' . DS . '.my.cnf';
		$hub_maint = DS . 'etc'  . DS . 'mysql' . DS . 'hubmaint.cnf';
		$deb_maint = DS . 'etc'  . DS . 'mysql' . DS . 'debian.cnf';

		if (is_file($secrets) && is_readable($secrets))
		{
			$conf = Processor::instance('ini')->parse($secrets);
			$user = (isset($conf['DEFAULT']['MYSQL-ROOT-USER'])) ? $conf['DEFAULT']['MYSQL-ROOT-USER'] : 'root';
			$pw   = (isset($conf['DEFAULT']['MYSQL-ROOT'])) ? $conf['DEFAULT']['MYSQL-ROOT'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($conf_file) && is_readable($conf_file))
		{
			$conf = Processor::instance('ini')->parse($conf_file, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($hub_maint) && is_readable($hub_maint))
		{
			$conf = Processor::instance('ini')->parse($hub_maint, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

			if ($user && $pw)
			{
				return array('user' => $user, 'password' => $pw);
			}
		}

		if (is_file($deb_maint) && is_readable($deb_maint))
		{
			$conf = Processor::instance('ini')->parse($deb_maint, true);
			$user = (isset($conf['client']['user'])) ? $conf['client']['user'] : false;
			$pw   = (isset($conf['client']['password'])) ? $conf['client']['password'] : false;

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
	 * @return  bool  If successfully upgraded to root access
	 **/
	public function runAsRoot()
	{
		if ($this->protectedMode)
		{
			return false;
		}

		if ($creds = $this->getRootCredentials())
		{
			$db = Driver::getInstance(
				array(
					'driver'   => (\Config::get('dbtype') == 'mysql') ? 'pdo' : \Config::get('dbtype'),
					'host'     => \Config::get('host'),
					'user'     => $creds['user'],
					'password' => $creds['password'],
					'database' => \Config::get('db'),
					'prefix'   => \Config::get('dbprefix')
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
	 * Set an error
	 *
	 * @param   string  $message
	 * @param   string  $type
	 * @return  void
	 **/
	public function setError($message, $type='fatal')
	{
		$this->errors[] = array('type' => $type, 'message' => $message);
	}

	/**
	 * Get errors
	 *
	 * @return  array  Errors
	 **/
	public function getErrors()
	{
		return $this->errors;
	}

	/**
	 * Get element params
	 *
	 * @param   string  $option     com_xyz
	 * @param   bool    $returnRaw  whether or not to return jregistry object or raw param string
	 * @return  object|string
	 **/
	public function getParams($element, $returnRaw=false)
	{
		if ($this->baseDb->tableExists('#__components'))
		{
			if (substr($element, 0, 4) == 'plg_')
			{
				$ext = explode("_", $element);
				$query = "SELECT `params` FROM `#__plugins` WHERE `folder` = " . $this->baseDb->quote($ext[1]) . " AND `element` = " . $this->baseDb->quote($ext[2]);
			}
			else
			{
				$query = "SELECT `params` FROM `#__components` WHERE `option` = " . $this->baseDb->quote($element);
			}

			$this->baseDb->setQuery($query);
			$params = $this->baseDb->loadResult();
		}
		else
		{
			if (substr($element, 0, 4) == 'plg_')
			{
				$ext = explode("_", $element);
				$query = "SELECT `params` FROM `#__extensions` WHERE `folder` = " . $this->baseDb->quote($ext[1]) . " AND `element` = " . $this->baseDb->quote($ext[2]);
			}
			else
			{
				$query = "SELECT `params` FROM `#__extensions` WHERE `element` = " . $this->baseDb->quote($element);
			}

			$this->baseDb->setQuery($query);
			$params = $this->baseDb->loadResult();
		}

		if (!$returnRaw)
		{
			if ($params)
			{
				$params = new Registry($params);
			}
			else
			{
				$params = new Registry();
			}
		}

		return $params;
	}

	/**
	 * Add, as needed, the component to the appropriate table, depending on the Joomla version
	 *
	 * @param   string  $name            Component name
	 * @param   string  $option          String) com_xyz
	 * @param   int     $enabled         Whether or not the component should be enabled
	 * @param   string  $params          Component params (if already known)
	 * @param   bool    $createMenuItem  Create an admin menu item for this component
	 * @return  bool
	 **/
	public function addComponentEntry($name, $option=NULL, $enabled=1, $params='', $createMenuItem=true)
	{
		if ($this->baseDb->tableExists('#__components'))
		{
			// First, make sure it isn't already there
			$query = "SELECT `id` FROM `#__components` WHERE `name` = " . $this->baseDb->quote($name);
			$this->baseDb->setQuery($query);
			if ($this->baseDb->loadResult())
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
			$query .= " VALUES ('{$name}', 'option={$option}', 0, 0, 'option={$option}', '{$name}', '{$option}', {$ordering}, '', 0, ".$this->baseDb->quote($params).", {$enabled})";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			if (is_null($option))
			{
				$option = 'com_' . strtolower($name);
			}
			$name = $option;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = " . $this->baseDb->quote($option);
			$this->baseDb->setQuery($query);
			if ($this->baseDb->loadResult())
			{
				$component_id = $this->baseDb->loadResult();
			}
			else
			{
				$ordering = 0;

				if (!empty($params) && is_array($params))
				{
					$params = json_encode($params);
				}

				$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
				$query .= " VALUES ('{$name}', 'component', '{$option}', '', 1, {$enabled}, 1, 0, '', ".$this->baseDb->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();
				$component_id = $this->baseDb->insertId();
			}

			// Secondly, add asset entry if not yet created
			$query = "SELECT `id` FROM `#__assets` WHERE `name` = " . $this->baseDb->quote($option);
			$this->baseDb->setQuery($query);
			if (!$this->baseDb->loadResult())
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
				$query = "SELECT `id` FROM `#__menu` WHERE `menutype` = 'main' AND `title` = " . $this->baseDb->quote($option);
				$this->baseDb->setQuery($query);
				if ($this->baseDb->loadResult())
				{
					return true;
				}

				$alias = substr($option, 4);

				$query = "INSERT INTO `#__menu` (`menutype`, `title`, `alias`, `note`, `path`, `link`, `type`, `published`, `parent_id`, `level`, `component_id`, `ordering`, `checked_out`, `checked_out_time`, `browserNav`, `access`, `img`, `template_style_id`, `params`, `lft`, `rgt`, `home`, `language`, `client_id`)";
				$query .= " VALUES ('main', '{$option}', '{$alias}', '', '{$alias}', 'index.php?option={$option}', 'component', {$enabled}, 1, 1, {$component_id}, 0, 0, '0000-00-00 00:00:00', 0, 0, '', 0, '', 0, 0, 0, '*', 1)";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();

				// If we have the nested set class available, use it to rebuild lft/rgt
				if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
				{
					// Use the MySQL driver for this
					$database = \JDatabase::getInstance(
						array(
							'driver'   => 'mysql',
							'host'     => \Config::get('host'),
							'user'     => \Config::get('user'),
							'password' => \Config::get('password'),
							'database' => \Config::get('db')
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
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   int     $enabled  Whether or not the plugin should be enabled
	 * @param   array   $params   Plugin params (if already known)
	 * @return  bool
	 **/
	public function addPluginEntry($folder, $element, $enabled=1, $params='')
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = ucfirst($folder) . ' - ' . ucfirst($element);

			// First, make sure it isn't already there
			$query = "SELECT `id` FROM `#__plugins` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			if ($this->baseDb->loadResult())
			{
				return true;
			}

			// Get ordering
			$query = "SELECT MAX(ordering) FROM `#__plugins` WHERE `folder` = " . $this->baseDb->quote($folder);
			$this->baseDb->setQuery($query);
			$ordering = (is_numeric($this->baseDb->loadResult())) ? $this->baseDb->loadResult()+1 : 1;

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
			$query .= " VALUES ('{$name}', '{$element}', '{$folder}', 0, {$ordering}, {$enabled}, 0, 0, 0, '0000-00-00 00:00:00', ".$this->baseDb->quote($params).")";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = 'plg_' . $folder . '_' . $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			if ($this->baseDb->loadResult())
			{
				return true;
			}

			// Get ordering
			$query = "SELECT MAX(ordering) FROM `#__extensions` WHERE `folder` = " . $this->baseDb->quote($folder);
			$this->baseDb->setQuery($query);
			$ordering = (is_numeric($this->baseDb->loadResult())) ? $this->baseDb->loadResult()+1 : 1;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
			$query .= " VALUES ('{$name}', 'plugin', '{$element}', '{$folder}', 0, {$enabled}, 1, 0, '', ".$this->baseDb->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Standardize a plugin entry name
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @return  bool
	 **/
	public function normalizePluginEntry($folder, $element)
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = ucfirst($folder) . ' - ' . ucfirst($element);

			return $this->renamePluginEntry($folder, $element, $name);
		}
		else if ($this->baseDb->tableExists('#__extensions'))
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = 'plg_' . $folder . '_' . $element;

			return $this->renamePluginEntry($folder, $element, $name);
		}
	}

	/**
	 * Rename a plugin entry in the appropriate table, depending on the Joomla version
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   string  $name     The new plugin name
	 * @return  bool
	 **/
	public function renamePluginEntry($folder, $element, $name)
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			$table = '#__plugins';
			$pk    = 'id';
		}
		else if ($this->baseDb->tableExists('#__extensions'))
		{
			$table = '#__extensions';
			$pk    = 'extension_id';
		}
		else
		{
			return false;
		}

		$folder  = strtolower($folder);
		$element = strtolower($element);

		// First, make sure the plugin exists
		$query = "SELECT `{$pk}` FROM `{$table}` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
		$this->baseDb->setQuery($query);
		if ($id = $this->baseDb->loadResult())
		{
			$query = "UPDATE `{$table}` SET `name` = " . $this->baseDb->quote($name) . " WHERE `{$pk}` = " . $this->baseDb->quote($id);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Save plugin params
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   array   $params   Plugin params (if already known)
	 * @return  void
	 **/
	public function savePluginParams($folder, $element, $params)
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = ucfirst($folder) . ' - ' . ucfirst($element);

			// First, make sure we have a plugin entry existing
			$query = "SELECT `id` FROM `#__plugins` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			if (!$id = $this->baseDb->loadResult())
			{
				$this->addPluginEntry($folder, $element, 1, $params);
				return;
			}

			// Build params string
			if (is_array($params))
			{
				$p = '';
				foreach ($params as $k => $v)
				{
					$p .= "{$k}={$v}\n";
				}

				$params = $p;
			}
			else
			{
				return false;
			}

			$query = "UPDATE `#__plugins` SET `params` = " . $this->baseDb->quote($params) . " WHERE `id` = " . $this->baseDb->quote($id);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			$folder  = strtolower($folder);
			$element = strtolower($element);
			$name    = 'plg_' . $folder . '_' . $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			if (!$id = $this->baseDb->loadResult())
			{
				$this->addPluginEntry($folder, $element, 1, $params);
				return;
			}

			// Build params JSON
			if (is_array($params))
			{
				$params = json_encode($params);
			}
			else
			{
				return false;
			}

			$query = "UPDATE `#__extensions` SET `params` = " . $this->baseDb->quote($params) . " WHERE `extension_id` = " . $this->baseDb->quote($id);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Saves extension params (only applies to J2.5 and up!)
	 *
	 * @param   string  $element  The element to which the params apply
	 * @param   array   $params   The params being saved
	 * @return  bool
	 **/
	public function saveParams($element, $params)
	{
		$element = strtolower($element);

		// First, make sure it's there
		if (substr($element, 0, 4) == 'plg_')
		{
			$ext   = explode("_", $element);
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `folder` = " . $this->baseDb->quote($ext[1]) . " AND `element` = " . $this->baseDb->quote($ext[2]);
		}
		else
		{
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `element` = " . $this->baseDb->quote($element);
		}

		$this->baseDb->setQuery($query);
		if (!$id = $this->baseDb->loadResult())
		{
			return false;
		}

		// Build params JSON
		if (is_array($params))
		{
			$params = json_encode($params);
		}
		else if ($params instanceof \JRegistry || $params instanceof Registry)
		{
			$params = $params->toString('JSON');
		}
		else
		{
			return false;
		}

		$query = "UPDATE `#__extensions` SET `params` = " . $this->baseDb->quote($params) . " WHERE `extension_id` = " . $this->baseDb->quote($id);
		$this->baseDb->setQuery($query);

		return $this->baseDb->query();
	}

	/**
	 * Add, as needed, the module entry to the appropriate table, depending on the Joomla version
	 *
	 * @param   string  $element  Plugin element
	 * @param   int     $enabled  Whether or not the plugin should be enabled
	 * @param   array   $params   Plugin params (if already known)
	 * @param   int     $client   Client (site=0, admin=1)
	 * @return  bool
	 **/
	public function addModuleEntry($element, $enabled=1, $params='', $client=0)
	{
		if ($this->baseDb->tableExists('#__extensions'))
		{
			$name = $element;

			// First, make sure it isn't already there
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `name` = " . $this->baseDb->quote($name);
			$this->baseDb->setQuery($query);
			if ($this->baseDb->loadResult())
			{
				return true;
			}

			$ordering = 0;

			if (!empty($params) && is_array($params))
			{
				$params = json_encode($params);
			}

			$query = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
			$query .= " VALUES ('{$name}', 'module', '{$element}', '', {$client}, {$enabled}, 1, 0, '', ".$this->baseDb->quote($params).", '', '', 0, '0000-00-00 00:00:00', {$ordering}, 0)";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Instead of just adding to the extensions table, install module in modules table
	 *
	 * @param   string  $module    Module name
	 * @param   string  $position  Module position
	 * @param   bool    $always    If true - always install, false - only install if another module of that type isn't present
	 * @param   array   $params    Params (if already known)
	 * @param   int     $client    Client (site=0, admin=1)
	 * @param   mixed   $menus     (int, array) menus to install to (0=all)
	 * @return  void
	 **/
	public function installModule($module, $position, $always=true, $params='', $client=0, $menus=0)
	{
		$title    = $this->baseDb->quote(ucfirst($module));
		$position = $this->baseDb->quote($position);
		$module   = $this->baseDb->quote('mod_' . strtolower($module));
		$client   = $this->baseDb->quote((int)$client);
		$access   = ($this->baseDb->tableExists('#__extensions')) ? 1 : 0;

		// Build params string
		if (is_array($params) && !$this->baseDb->tableExists('#__extensions'))
		{
			$p = '';
			foreach ($params as $k => $v)
			{
				$p .= "{$k}={$v}\n";
			}

			$params = $this->baseDb->quote($p);
		}
		else
		{
			$params = $this->baseDb->quote(json_encode($params));
		}

		if (!$always)
		{
			$query = "SELECT `id` FROM `#__modules` WHERE `module` = {$module}";
			$this->db->setQuery($query);

			if ($this->db->loadResult())
			{
				return true;
			}
		}

		$query = "SELECT MAX(ordering) FROM `#__modules` WHERE `position` = {$position}";
		$this->baseDb->setQuery($query);
		$ordering = (int)(($this->baseDb->loadResult()) ? $this->baseDb->loadResult() + 1 : 0);

		$query  = "INSERT INTO `#__modules` ";
		$query .= "(`title` , `content`, `ordering` , `position` , `published`, `module` , `access` , `showtitle`, `params` , `client_id`) VALUES ";
		$query .= "({$title}, ''       , {$ordering}, {$position}, 1          , {$module}, {$access}, 0          , {$params}, {$client}  )";

		$this->baseDb->setQuery($query);
		$this->baseDb->query();
		$id = $this->baseDb->quote($this->baseDb->insertid());

		$menus = (array)$menus;
		foreach ($menus as $menu)
		{
			$menu  = $this->baseDb->quote($menu);
			$query = "INSERT INTO `#__modules_menu` (`moduleid`, `menuid`) VALUES ({$id}, {$menu})";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Add, as needed, templates to the CMS
	 *
	 * @param   string  $element    Template element
	 * @param   string  $name       Template name
	 * @param   int     $client     Admin or site client
	 * @param   int     $enabled    Whether or not the template should be enabled
	 * @param   int     $home       Whether or not this should become the enabled template
	 * @param   array   $styles     Template styles
	 * @param   int     $protected  Whether or not the template is a core one or not
	 * @return  bool
	 **/
	public function addTemplateEntry($element, $name=null, $client=1, $enabled=1, $home=0, $styles=NULL, $protected=0)
	{
		if ($this->baseDb->tableExists('#__extensions'))
		{
			if (!isset($name))
			{
				if (substr($element, 0, 4) == 'tpl_')
				{
					$name    = substr($element, 4);
					$element = $name;
				}
				else
				{
					$name = $element;
				}

				$name = ucwords($name);
			}

			// First, see if it already exists
			$query = "SELECT `extension_id` FROM `#__extensions` WHERE `type` = 'template' AND (`element` = '{$element}' OR `element` LIKE '{$name}') AND `client_id` = '{$client}'";
			$this->baseDb->setQuery($query);

			if (!$this->baseDb->loadResult())
			{
				$query  = "INSERT INTO `#__extensions` (`name`, `type`, `element`, `folder`, `client_id`, `enabled`, `access`, `protected`, `manifest_cache`, `params`, `custom_data`, `system_data`, `checked_out`, `checked_out_time`, `ordering`, `state`)";
				$query .= " VALUES ('{$name}', 'template', '{$element}', '', '{$client}', '{$enabled}', '1', '{$protected}', '{}', '{}', '', '', '0', '0000-00-00 00:00:00', '0', '0')";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();

				// If we're setting this template to be default, disable others first
				if ($home)
				{
					$query = "UPDATE `#__template_styles` SET `home` = 0 WHERE `client_id` = '{$client}'";
					$this->baseDb->setQuery($query);
					$this->baseDb->query();
				}

				$query  = "INSERT INTO `#__template_styles` (`template`, `client_id`, `home`, `title`, `params`)";
				$query .= " VALUES ('{$element}', '{$client}', '{$home}', '{$name}', " . ((isset($styles)) ? $this->baseDb->quote(json_encode($styles)) : "'{}'") . ")";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();
			}
		}
	}

	/**
	 * Install a template, adding it if needed
	 *
	 * @param   string  $element    Template element
	 * @param   string  $name       Template name
	 * @param   int     $client     Admin or site client
	 * @param   array   $styles     Template styles
	 * @param   int     $protected  Whether or not the template is a core one or not
	 * @return  void
	 **/
	public function installTemplate($element, $name=null, $client=1, $styles=NULL, $protected=0)
	{
		$this->addTemplateEntry($element, $name, $client, 1, 1, $styles, $protected);
	}

	/**
	 * Sets the asset rules
	 *
	 * @param   string  $element  The element to which the rules apply
	 * @param   array   $rules    The incoming rules to set
	 * @return  void
	 **/
	public function setAssetRules($element, $rules)
	{
		if ($this->baseDb->tableExists('#__assets'))
		{
			$asset = \JTable::getInstance('Asset');
			if (!$asset->loadByName($element))
			{
				return false;
			}

			// Loop through and map textual groups to ids (if applicable)
			{
				foreach ($rules as $idx => $rule)
				{
					foreach ($rule as $group => $value)
					{
						if (!is_numeric($group))
						{
							$query = "SELECT `id` FROM `#__usergroups` WHERE `title` = " . $this->baseDb->quote($group);
							$this->baseDb->setQuery($query);
							if ($id = $this->baseDb->loadResult())
							{
								unset($rules[$idx][$group]);
								$rules[$idx][$id] = $value;
							}
						}
					}
				}
			}

			$asset->rules = json_encode($rules);
			$asset->store();
		}
	}

	/**
	 * Remove component entries from the appropriate table, depending on the Joomla version
	 *
	 * @param   string  $name  Component name
	 * @return  bool
	 **/
	public function deleteComponentEntry($name)
	{
		if ($this->baseDb->tableExists('#__components'))
		{
			// Delete component entry
			$query = "DELETE FROM `#__components` WHERE `name` = " . $this->baseDb->quote($name);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			$name = 'com_' . strtolower($name);
			// Delete component entry
			$query = "DELETE FROM `#__extensions` WHERE `name` = " . $this->baseDb->quote($name);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			// Remove the component container in the assets table
			$asset = \JTable::getInstance('Asset');
			if ($asset->loadByName($name))
			{
				$asset->delete();
			}

			// Check for an admin menu entry...if it's not there, create it
			$query = "DELETE FROM `#__menu` WHERE `menutype` = 'main' AND `title` = " . $this->baseDb->quote($name);
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			// If we have the nested set class available, use it to rebuild lft/rgt
			if (class_exists('JTableNested') && method_exists('JTableNested', 'rebuild'))
			{
				// Use the MySQL driver for this
				$database = \JDatabase::getInstance(
					array(
						'driver'   => 'mysql',
						'host'     => \Config::get('host'),
						'user'     => \Config::get('user'),
						'password' => \Config::get('password'),
						'database' => \Config::get('db')
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
	 * @param   string  $name  Plugin name
	 * @return  bool
	 **/
	public function deletePluginEntry($folder, $element=NULL)
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			// Delete plugin(s) entry
			$query = "DELETE FROM `#__plugins` WHERE `folder` = " . $this->baseDb->quote($folder) . ((!is_null($element)) ? " AND `element` = '{$element}'" : "");
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			// Delete plugin(s) entry
			$query = "DELETE FROM `#__extensions` WHERE `folder` = " . $this->baseDb->quote($folder) . ((!is_null($element)) ? " AND `element` = '{$element}'" : "");
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Remove module entries from the appropriate table, depending on the Joomla version
	 *
	 * @param   string  $name    Plugin name
	 * @param   int     $client  Client (site=0, admin=1)
	 * @return  bool
	 **/
	public function deleteModuleEntry($element, $client=null)
	{
		if ($this->baseDb->tableExists('#__extensions'))
		{
			// Delete module entry
			$query = "DELETE FROM `#__extensions` WHERE `element` = '{$element}'" . ((isset($client)) ? " AND `client_id` = " . $this->baseDb->quote($client) : '');
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			// See if entries are present in #__modules table as well
			$query = "SELECT `id` FROM `#__modules` WHERE `module` = '{$element}'" . ((isset($client)) ? " AND `client_id` = " . $this->baseDb->quote($client) : '');
			$this->baseDb->setQuery($query);
			$ids = $this->baseDb->loadColumn();

			if ($ids && count($ids) > 0)
			{
				// Delete modules and module menu entries
				$query = "DELETE FROM `#__modules` WHERE `id` IN (" . implode(',', $ids) . ")";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();

				$query = "DELETE FROM `#__modules_menu` WHERE `moduleid` IN (" . implode(',', $ids) . ")";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();
			}
		}
		else
		{
			$query = "SELECT `id` FROM `#__modules` WHERE `module` = '{$element}'" . ((isset($client)) ? " AND `client_id` = " . $this->baseDb->quote($client) : '');
			$this->baseDb->setQuery($query);
			$ids = $this->baseDb->loadColumn();

			if ($ids && count($ids) > 0)
			{
				// Delete modules and module menu entries
				$query = "DELETE FROM `#__modules` WHERE `id` IN (" . implode(',', $ids) . ")";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();

				$query = "DELETE FROM `#__modules_menu` WHERE `moduleid` IN (" . implode(',', $ids) . ")";
				$this->baseDb->setQuery($query);
				$this->baseDb->query();
			}
		}
	}

	/**
	 * Remove template entires from the appropriate tables
	 *
	 * @param   string  $name    Template element name
	 * @param   int     $client  Client id
	 * @return  bool
	 **/
	public function deleteTemplateEntry($element, $client=1)
	{
		if ($this->baseDb->tableExists('#__extensions'))
		{
			$query = "DELETE FROM `#__extensions` WHERE `type` = 'template' AND `element` = '{$element}' AND `client_id` = '{$client}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			$query = "DELETE FROM `#__template_styles` WHERE `template` = '{$element}' AND `client_id` = '{$client}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			// Now make sure we have an enabled template (don't really care which one it is)
			$query = "SELECT `id` FROM `#__template_styles` WHERE `home` = 1 AND `client_id` = '{$client}'";
			$this->baseDb->setQuery($query);
			if (!$this->baseDb->loadResult())
			{
				$query = "SELECT `id` FROM `#__template_styles` WHERE `client_id` = '{$client}' ORDER BY `id` DESC LIMIT 1";
				$this->baseDb->setQuery($query);
				if ($id = $this->baseDb->loadResult())
				{
					$query = "UPDATE `#__template_styles` SET `home` = 1 WHERE `id` = '{$id}'";
					$this->baseDb->setQuery($query);
					$this->baseDb->query();
				}
			}
		}
	}

	/**
	 * Enable plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @return  void
	 **/
	public function enablePlugin($folder, $element)
	{
		$this->setPluginStatus($folder, $element, 1);
	}

	/**
	 * Disable plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @return  void
	 **/
	public function disablePlugin($folder, $element)
	{
		$this->setPluginStatus($folder, $element, 0);
	}

	/**
	 * Enable/disable plugin
	 *
	 * @param   string  $folder   Plugin folder
	 * @param   string  $element  Plugin element
	 * @param   int     $enabled  Whether or not the plugin should be enabled
	 * @return  void
	 **/
	private function setPluginStatus($folder, $element, $enabled=1)
	{
		if ($this->baseDb->tableExists('#__plugins'))
		{
			$query = "UPDATE `#__plugins` SET `published` = '{$enabled}' WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `enabled` = '{$enabled}' WHERE `folder` = '{$folder}' AND `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Enable component
	 *
	 * @param   string  $element  Element
	 * @return  void
	 **/
	public function enableComponent($element)
	{
		$this->setComponentStatus($element);
	}

	/**
	 * Disable component
	 *
	 * @param   string  $element  Element
	 * @return  void
	 **/
	public function disableComponent($element)
	{
		$this->setComponentStatus($element, 0);
	}

	/**
	 * Enable/disable component
	 *
	 * @param   string  $element  Element
	 * @param   int     $enabled  Whether or not the component should be enabled
	 * @return  void
	 **/
	private function setComponentStatus($element, $enabled=1)
	{
		if ($this->baseDb->tableExists('#__components'))
		{
			$query = "UPDATE `#__components` SET `enabled` = '{$enabled}' WHERE `option` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
		else
		{
			$query = "UPDATE `#__extensions` SET `enabled` = '{$enabled}' WHERE `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}

	/**
	 * Enable module
	 *
	 * @param   string  $element  Element
	 * @return  void
	 **/
	public function enableModule($element)
	{
		$this->setModuleStatus($element);
	}

	/**
	 * Disable module
	 *
	 * @param   string  $element  Element
	 * @return  void
	 **/
	public function disableModule($element)
	{
		$this->setModuleStatus($element, 0);
	}

	/**
	 * Enable/disable module
	 *
	 * @param  string  $element  Element
	 * @param  int     $enabled  Whether or not the module should be enabled
	 * @return void
	 **/
	private function setModuleStatus($element, $enabled=1)
	{
		if ($this->baseDb->tableExists('#__extensions'))
		{
			$query = "UPDATE `#__extensions` SET `enabled` = '{$enabled}' WHERE `element` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();

			$query = "UPDATE `#__modules` SET `published` = '{$enabled}' WHERE `module` = '{$element}'";
			$this->baseDb->setQuery($query);
			$this->baseDb->query();
		}
	}
}
