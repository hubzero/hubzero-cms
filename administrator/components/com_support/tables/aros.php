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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class SupportAro extends JTable
{
	var $id      = NULL;  // @var int(11) Primary key
	var $model   = NULL;  // @var varchar(100)
	var $foreign_key = NULL;  // @var int(11)
	var $alias   = NULL;  // @var varchar(250)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__support_acl_aros', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->model ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD').': model' );
			return false;
		}
		if (trim( $this->foreign_key ) == '') {
			$this->setError( JText::_('SUPPORT_ERROR_BLANK_FIELD').': foreign_key' );
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

