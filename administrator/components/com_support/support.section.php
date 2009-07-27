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
// Extended database class
//----------------------------------------------------------

class SupportSection extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $section = NULL;  // @var varchar(50)

	//-----------

	function __construct( &$db ) 
	{
		parent::__construct( '#__support_sections', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->section ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function getSections() 
	{
		$this->_db->setQuery( "SELECT id, section AS txt FROM $this->_tbl ORDER BY id");
		return $this->_db->loadObjectList();
	}

	//-----------

	function buildQuery( $filters=array() ) 
	{
		$query = " FROM $this->_tbl"
				. " ORDER BY section";
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	function getCount( $filters=array() ) 
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() )
	{
		$query  = "SELECT id, section";
		$query .= $this->buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
?>