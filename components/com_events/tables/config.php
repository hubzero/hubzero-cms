<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
