<?php
/**
 * @package		HUBzero CMS
 * @author		Nicholas J. Kisseberth <nkissebe@purdue.edu>
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

class XOrganization extends JTable 
{
	var $id = null;
	var $organization = null;
	
	//-----------
	
	function __construct( &$db ) 
	{
		parent::__construct( '#__xorganizations', 'id', $db );
	}
	
	//-----------
	
	function check() 
	{
		if (trim( $this->organization ) == '') {
			$this->setError( JText::_('Organization must contain text') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " WHERE organization LIKE '%".$filters['search']."%'";
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	function getRecords( $filters=array() ) 
	{
		$query  = "SELECT * FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " WHERE organization LIKE '%".$filters['search']."%'";
		}
		$query .= " ORDER BY organization ASC";
		if (isset($filters['limit']) && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	function getOrgs( $filters=array() ) 
	{
		$os = $this->getRecords($filters);

		$orgs = array();
		if ($os) {
			foreach ($os as $o) 
			{
				$orgs[] = $o->organization;
			}
		}
		
		return $orgs;
	}
}
?>