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

	private function _buildQuery( $filters=array() )
	{
		$query = " FROM $this->_tbl ORDER BY id";
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}

	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*)";
		$query .= $this->_buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	public function getRecords( $filters=array() )
	{
		$query  = "SELECT *";
		$query .= $this->_buildQuery( $filters );
		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

