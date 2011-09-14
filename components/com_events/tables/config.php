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
defined('_JEXEC') or die( 'Restricted access' );

/**
 * Short description for 'EventsConfig'
 * 
 * Long description (if any) ...
 */
class EventsConfig extends JTable
{

	/**
	 * Description for 'param'
	 * 
	 * @var unknown
	 */
	var $param = NULL;

	/**
	 * Description for 'value'
	 * 
	 * @var unknown
	 */
	var $value = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__events_config', 'param', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function check()
	{
		// check for valid name
		if (trim( $this->param ) == '') {
			$this->setError( JText::_('EVENTS_BLANK_CONFIG_PARAMETER') );
			return false;
		}
		return true;
	}
}

/**
 * Short description for 'class'
 * 
 * Long description (if any) ...
 */
class EventsConfigs
{

	/**
	 * Description for '_tbl'
	 * 
	 * @var string
	 */
	private $_tbl   = NULL;

	/**
	 * Description for '_db'
	 * 
	 * @var object
	 */
	private $_db    = NULL;

	/**
	 * Description for '_data'
	 * 
	 * @var array
	 */
	private $_data  = array();

	/**
	 * Description for '_error'
	 * 
	 * @var unknown
	 */
	private $_error = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		$this->_tbl = '#__events_config';
		$this->_db = $db;
	}

	/**
	 * Short description for '__set'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @param      unknown $value Parameter description (if any) ...
	 * @return     void
	 */
	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}

	/**
	 * Short description for '__get'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $property Parameter description (if any) ...
	 * @return     array Return description (if any) ...
	 */
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	/**
	 * Short description for 'load'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     void
	 */
	public function load()
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl" );
		$configs = $this->_db->loadObjectList();

		if (empty($configs) || count($configs) <= 0) {
			if ($this->loadDefaults()) {
				$this->_db->setQuery( "SELECT * FROM $this->_tbl" );
				$configs = $this->_db->loadObjectList();
			}
		}

		if (!empty($configs)) {
			foreach ($configs as $config)
			{
				$b = $config->param;
				$this->$b = trim($config->value);
			}
		}

		$fields = array();
		if (trim($this->fields) != '') {
			$fs = explode("\n", trim($this->fields));
			foreach ($fs as $f)
			{
				$fields[] = explode('=', $f);
			}
		}
		$this->fields = $fields;
	}

	/**
	 * Short description for 'loadDefaults'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
	public function loadDefaults()
	{
		$config = array(
				'adminmail'=>'',
				'adminlevel'=>'0',
				'starday'=>'0',
				'mailview'=>'NO',
				'byview'=>'NO',
				'hitsview'=>'NO',
				'repeatview'=>'NO',
				'dateformat'=>'0',
				'calUseStdTime'=>'NO',
				'navbarcolor'=>'',
				'startview'=>'month',
				'calEventListRowsPpg'=>'30',
				'calSimpleEventForm'=>'NO',
				'defColor'=>'',
				'calForceCatColorEventForm'=>'NO',
				'fields'=>''
			);
		foreach ($config as $p=>$v)
		{
			$this->_db->setQuery( "INSERT INTO $this->_tbl (param, value) VALUES ('$p', '$v')" );
			if (!$this->_db->query()) {
				return false;
			}
		}
		return true;
	}

	/**
	 * Short description for 'getCfg'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $f Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function getCfg( $f='' )
	{
		if ($f) {
			return $this->$f;
		} else {
			return NULL;
		}
	}
}

