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


class TagsGroup extends JTable
{
	var $id      = NULL;  // int(11)
	var $groupid = NULL;  // int(11)
	var $tagid   = NULL;  // int(11)
	var $priority = NULL;  // int(11)
	
	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__tags_group', 'id', $db );
	}

	//-----------
	
	public function getCount() 
	{
		$query = "SELECT COUNT(*) 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
		
	//-----------
	
	public function getRecords() 
	{
		$query = "SELECT tg.id, t.tag, g.cn, g.description, tg.tagid, tg.groupid, tg.priority 
					FROM $this->_tbl AS tg,
					#__tags AS t,
					#__xgroups as g
					WHERE tg.tagid=t.id 
					AND g.gidNumber=tg.groupid ORDER BY tg.priority ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
	
	//-----------
	
	public function getNeighbor( $move ) 
	{
		switch ($move) 
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE priority < ".$this->priority." ORDER BY priority DESC LIMIT 1";
				break;
			
			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE priority > ".$this->priority." ORDER BY priority ASC LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}
