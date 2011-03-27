<?php
/**
 * @package     hubzero-cms
 * @author      Shawn Rice <zooley@purdue.edu>
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );

class EventsConfig extends JTable 
{
	var $param = NULL;
	var $value = NULL;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__events_config', 'param', $db );
	}

	//-----------
	
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

class EventsConfigs 
{
	private $_tbl   = NULL;
	private $_db    = NULL;
	private $_data  = array();
	private $_error = NULL;

	//-----------
	
	public function __construct( &$db )
	{
		$this->_tbl = '#__events_config';
		$this->_db = $db;
	}

	//-----------

	public function __set($property, $value)
	{
		$this->_data[$property] = $value;
	}
	
	//-----------
	
	public function __get($property)
	{
		if (isset($this->_data[$property])) {
			return $this->_data[$property];
		}
	}

	//-----------
	
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
	
	//-----------
	
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
	
	//-----------
	
	public function getCfg( $f='' ) 
	{
		if ($f) {
			return $this->$f;
		} else {
			return NULL;
		}
	}
}

