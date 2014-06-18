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
 * Events table class for configuration
 */
class EventsConfig extends JTable
{
	/**
	 * varchar(100)
	 *
	 * @var string
	 */
	var $param = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $value = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_config', 'param', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->param) == '')
		{
			$this->setError(JText::_('EVENTS_BLANK_CONFIG_PARAMETER'));
			return false;
		}
		return true;
	}
}

/**
 * Events class for getting all configurations
 */
class EventsConfigs
{
	/**
	 * Table name
	 *
	 * @var string
	 */
	private $_tbl   = NULL;

	/**
	 * JDatavase
	 *
	 * @var object
	 */
	private $_db    = NULL;

	/**
	 * Container for data
	 *
	 * @var array
	 */
	private $_data  = array();

	/**
	 * Container for error messages
	 *
	 * @var unknown
	 */
	private $_error = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		$this->_tbl = '#__events_config';
		$this->_db  = $db;
	}

	/**
	 * Method to set an overloaded variable to the component
	 *
	 * @param	string	$property	Name of overloaded variable to add
	 * @param	mixed	$value 		Value of the overloaded variable
	 * @return	void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Method to get an overloaded variable of the component
	 *
	 * @param	string	$property	Name of overloaded variable to retrieve
	 * @return	mixed 	Value of the overloaded variable
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property]))
		{
			return $this->_data[$property];
		}
	}

	/**
	 * Get all configurations and populate $this
	 *
	 * @return     void
	 */
	public function load()
	{
		$this->_db->setQuery("SELECT * FROM $this->_tbl");
		$configs = $this->_db->loadObjectList();

		if (empty($configs) || count($configs) <= 0)
		{
			if ($this->loadDefaults())
			{
				$this->_db->setQuery("SELECT * FROM $this->_tbl");
				$configs = $this->_db->loadObjectList();
			}
		}

		if (!empty($configs))
		{
			foreach ($configs as $config)
			{
				$b = $config->param;
				$this->$b = trim($config->value);
			}
		}

		$fields = array();
		if (trim($this->fields) != '')
		{
			$fs = explode("\n", trim($this->fields));
			foreach ($fs as $f)
			{
				$fields[] = explode('=', $f);
			}
		}
		$this->fields = $fields;
	}

	/**
	 * Set the default configuration values
	 *
	 * @return     boolean True on success, false on errors
	 */
	public function loadDefaults()
	{
		$config = array(
			'adminmail' => '',
			'adminlevel' => '0',
			'starday' => '0',
			'mailview' => 'NO',
			'byview' => 'NO',
			'hitsview' => 'NO',
			'repeatview' => 'NO',
			'dateformat' => '0',
			'calUseStdTime' => 'NO',
			'navbarcolor' => '',
			'startview' => 'month',
			'calEventListRowsPpg' => '30',
			'calSimpleEventForm' => 'NO',
			'defColor' => '',
			'calForceCatColorEventForm' => 'NO',
			'fields' => ''
		);
		foreach ($config as $p => $v)
		{
			$this->_db->setQuery("INSERT INTO $this->_tbl (param, value) VALUES (" . $this->_db->Quote($p) . ", " . $this->_db->Quote($v) . ")");
			if (!$this->_db->query())
			{
				return false;
			}
		}
		return true;
	}

	/**
	 * Get a configuration value
	 *
	 * @param      string $f Property name
	 * @return     string
	 */
	public function getCfg($f='')
	{
		if ($f)
		{
			return $this->$f;
		}
		else
		{
			return NULL;
		}
	}
}

