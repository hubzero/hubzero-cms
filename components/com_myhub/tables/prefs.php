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
