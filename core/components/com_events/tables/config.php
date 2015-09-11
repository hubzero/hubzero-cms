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

namespace Components\Events\Tables;

include_once(__DIR__ . DS . 'configs.php');

/**
 * Events table class for configuration
 */
class Config extends \JTable
{
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
			$this->setError(Lang::txt('EVENTS_BLANK_CONFIG_PARAMETER'));
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
			$this->_db->setQuery("INSERT INTO $this->_tbl (param, value) VALUES (" . $this->_db->quote($p) . ", " . $this->_db->quote($v) . ")");
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

