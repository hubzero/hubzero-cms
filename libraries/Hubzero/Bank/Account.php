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


class Hubzero_Bank_Account extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $uid      = NULL;  // @var int(11)
	var $balance  = NULL;  // @var decimal(11,2)
	var $earnings = NULL;  // @var decimal(11,2)
	var $credit   = NULL;  // @var decimal(11,2)

	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__users_points', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Entry must have a user ID.') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function load_uid( $oid=NULL ) 
	{
		if ($oid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uid='$oid'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
