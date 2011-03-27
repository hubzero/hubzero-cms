<?php
/**
 * @package     hubzero-cms
 * @author      Nicholas J. Kisseberth <nkissebe@purdue.edu>
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


class XOrganization extends JTable 
{
	var $id = null;
	var $organization = null;
	
	//-----------
	
	public function __construct( &$db ) 
	{
		parent::__construct( '#__xorganizations', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->organization ) == '') {
			$this->setError( JText::_('Organization must contain text') );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl";
		if (isset($filters['search']) && $filters['search'] != '') {
			$query .= " WHERE organization LIKE '%".$filters['search']."%'";
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
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
	
	public function getOrgs( $filters=array() ) 
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

