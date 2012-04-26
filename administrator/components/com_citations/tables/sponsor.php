<?php
/**
 * HUBzero CMS
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
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' );


class CitationsSponsor extends JTable
{
	var $id = null;
	var $sponsor = null;
	var $link = null;
	
	//-----
	
	function __construct( &$db )
	{
		parent::__construct( '#__citations_sponsors', 'id', $db );
	}
	
	//-----
	
	public function getSponsor( $id = '' )
	{
		$where = (is_numeric($id)) ? "WHERE id='{$id}'" : "";

		$sql = "SELECT * FROM {$this->_tbl} {$where} ORDER BY sponsor";
		$this->_db->setQuery( $sql );
		$results = $this->_db->loadAssocList();

		return $results;
	}
	
	//-----
	
	public function getCitationSponsor( $citeid )
	{
		if(!$citeid)
		{
			return;
		}
		
		$sql = "SELECT sid FROM #__citations_sponsors_assoc WHERE cid=".$citeid;
		$this->_db->setQuery( $sql );
		$results = $this->_db->loadResultArray();
		
		return $results;
	}
	
	//-----
	
	public function addSponsors( $citeid, $sponsors )
	{
		if(!$citeid)
		{
			return;
		}
		
		// remove any existing associations
		$sql = "DELETE FROM #__citations_sponsors_assoc WHERE cid=".$citeid;
		$this->_db->setQuery( $sql );
		$this->_db->query();
		
		//add all new associations
		$sql = "INSERT INTO #__citations_sponsors_assoc(cid, sid) VALUES";
		foreach($sponsors as $s)
		{
			$sql .= "({$citeid}, {$s}), ";
		}
		$sql = substr($sql, 0, -2) . ";";
		$this->_db->setQuery( $sql );
		$this->_db->query();
		
		return true;
	}
}
?>
