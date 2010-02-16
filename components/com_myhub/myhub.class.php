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

//----------------------------------------------------------
// Myhub Database classes
//----------------------------------------------------------

class MyhubPrefs extends JTable
{
	var $uid   = NULL;  // int(11)
	var $prefs = NULL;  // varchar(200)
	var $modified = NULL;  // datetime(0000-00-00 00:00:00)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__myhub', 'uid', $db );
	}
	
	//-----------

	public function check() 
	{
		if (!$this->uid) {
			$this->setError( JText::_('ERROR_NO_USER_ID') );
			return false;
		}
		
		return true;
	}
	
	//-----------
	
	public function create() 
	{
		$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		if (!$ret) {
			$this->setError(get_class( $this ).'::create failed - '.$this->_db->getErrorMsg());
			return false;
		} else {
			return true;
		}
	}
	
	//-----------
	
	public function getPrefs( $module=NULL ) 
	{
		if (!$module) {
			return false;
		}
		
		$sql = "SELECT * 
				FROM $this->_tbl 
				WHERE `prefs` NOT LIKE '%,$module,%' 
				AND `prefs` NOT LIKE '$module,%' 
				AND `prefs` NOT LIKE '$module;%'
				AND `prefs` NOT LIKE '%,$module;%' 
				AND `prefs` NOT LIKE '%,$module' 
				AND `prefs` NOT LIKE '%;$module,%' 
				AND `prefs` NOT LIKE '%;$module;%'
				AND `prefs` NOT LIKE '%;$module'";
		
		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}
}


class MyhubParams extends JTable
{
	var $uid    = NULL;  // int(11)
	var $mid    = NULL;  // int(11)
	var $params = NULL;  // text

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__myhub_params', 'uid', $db );
	}
	
	//-----------

	public function check() 
	{
		if (!$this->uid) {
			$this->setError( JText::_('ERROR_NO_USER_ID') );
			return false;
		}
		
		if (!$this->mid) {
			$this->setError( JText::_('ERROR_NO_MOD_ID') );
			return false;
		}
		
		return true;
	}

	//-----------
	
	public function loadParams( $uid=NULL, $mid=NULL ) 
	{
		if ($uid === NULL) {
			return false;
		}
		if ($mid === NULL) {
			return false;
		}
		
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$uid' AND mid='$mid' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
	
	//-----------

	public function storeParams( $new=false ) 
	{
		if (!$new) {
			$this->_db->setQuery( "UPDATE $this->_tbl SET params='$this->params' WHERE uid=".$this->uid." AND mid=".$this->mid);
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {
			$this->_db->setQuery( "INSERT INTO $this->_tbl (uid,mid,params) VALUES ($this->uid,$this->mid,'$this->params')" );
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		}
		if (!$ret) {
			$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}
	
	//-----------
	
	public function loadModule( $uid=NULL, $mid=NULL ) 
	{
		if ($uid === NULL) {
			return false;
		}
		if ($mid === NULL) {
			return false;
		}
		
		include_once( JPATH_ROOT.DS.'libraries'.DS.'joomla'.DS.'database'.DS.'table'.DS.'module.php' );
		$jmodule = new JTableModule( $this->_db );
		
		$query = "SELECT m.*, p.params AS myparams"
				. " FROM ".$jmodule->getTableName()." AS m"
				. " LEFT JOIN $this->_tbl AS p ON m.id=p.mid AND p.uid=".$uid.""
				. " WHERE m.id='".$mid."' LIMIT 1";
		$this->_db->setQuery( $query );
		$modules = $this->_db->loadObjectList();
		if (!empty($modules)) {
			return $modules[0];
		} else {
			return false;
		}
	}
}
?>