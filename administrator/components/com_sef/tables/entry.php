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

class SefEntry extends JTable
{
	var $id	     = null;  // @var int
	var $cpt     = null;  // @var int
	var $oldurl	 = null;  // @var string
	var $newurl	 = null;  // @var string
	var $dateadd = null;  // @var date

	//-----------

	public function __construct( &$_db )
	{
		parent::__construct( '#__redirection', 'id', $_db );
	}

	public function getCount( $filters=array(), $admin=false )
	{
		$sql  = "SELECT COUNT(*) ";
		$sql .= $this->buildQuery( $filters, $admin );

		$this->_db->setQuery( $sql );
		return $this->_db->loadResult();
	}

	public function getRecords( $filters=array(), $admin=false )
	{
		$sql  = "SELECT * ";
		$sql .= $this->buildQuery( $filters, $admin );
		$sql .= " LIMIT " . $filters['start'] . ", " . $filters['limit'];

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	public function buildQuery( $filters=array(), $admin=false )
	{
		$query = "FROM $this->_tbl WHERE ";
		if ($filters['ViewModeId'] == 1) {
			$query .= "`dateadd` > '0000-00-00' AND `newurl` = '' ";
		} elseif ( $filters['ViewModeId'] == 2 ) {
			$query .= "`dateadd` > '0000-00-00' AND `newurl` != '' ";
		} else {
			$query .= "`dateadd` = '0000-00-00'";
		}
		$query .= " ORDER BY ";
		switch ($filters['SortById'])
		{
			case 1 :
				$query .= "`oldurl` DESC";
				break;
			case 2 :
				$query .= "`newurl`";
				break;
			case 3 :
				$query .= "`newurl` DESC";
				break;
			case 4 :
				$query .= "`cpt`";
				break;
			case 5 :
				$query .= "`cpt` DESC";
				break;
			default :
				$query .= "`oldurl`";
				break;
		}
		return $query;
	}
}

