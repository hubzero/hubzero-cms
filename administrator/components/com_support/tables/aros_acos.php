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


class SupportAroAco extends JTable 
{
	var $id      = NULL;  // @var int(11) Primary key
	var $aro_id  = NULL;  // @var int(11)
	var $aco_id  = NULL;  // @var int(11)
	var $action_create = NULL;  // @var int(3)
	var $action_read   = NULL;  // @var int(3)
	var $action_update = NULL;  // @var int(3)
	var $action_delete = NULL;  // @var int(3)

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__support_acl_aros_acos', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->aro_id ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD').': aro_id' );
			return false;
		}
		if (trim( $this->aco_id ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD').': aco_id' );
			return false;
		}

		return true;
	}
	
	//-----------
	
	public function deleteRecordsByAro( $aro_id=0 )
	{
		if (!$aro_id) {
			$this->setError( JText::_('Missing ARO ID') );
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE aro_id=$aro_id" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function deleteRecordsByAco( $aco_id=0 )
	{
		if (!$aco_id) {
			$this->setError( JText::_('Missing ACO ID') );
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE aco_id=$aco_id" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}
	
	//-----------
	
	private function _buildQuery( $filters=array() ) 
	{
		$query = " FROM $this->_tbl ORDER BY id";
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() ) 
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() )
	{
		$query  = "SELECT *";
		$query .= $this->_buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
