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

