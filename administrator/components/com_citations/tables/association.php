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


class CitationsAssociation extends JTable 
{
	var $id    = NULL;  // @var int(11) Primary key
	var $cid   = NULL;  // @var int(11)
	var $oid   = NULL;  // @var varchar(200)
	var $type  = NULL;  // @var int(3)
	var $table = NULL;  // @var int(3)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__citations_assoc', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->cid ) == '') {
			$this->setError( JText::_('ASSOCIATION_MUST_HAVE_CITATION_ID') );
			return false;
		}
		if (trim( $this->oid ) == '') {
			$this->setError( JText::_('ASSOCIATION_MUST_HAVE_OBJECT_ID') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function buildQuery( $filters ) 
	{
		$query = "";
		
		$ands = array();
		if (isset($filters['cid']) && $filters['cid'] != 0) {
			$ands[] = "r.cid='".$filters['cid']."'";
		}
		if (isset($filters['oid']) && $filters['oid'] != 0) {
			$ands[] = "r.oid='".$filters['oid']."'";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$ands[] = "r.type='".$filters['type']."'";
		}
		if (isset($filters['type']) && $filters['type'] != '') {
			$ands[] = "r.type='".$filters['type']."'";
		}
		if (isset($filters['table']) && $filters['table'] != '') {
			$ands[] = "r.table='".$filters['table']."'";
		}
		if (count($ands) > 0) {
			//$query .= " WHERE r.published=1 AND ";
			$query .= " WHERE ";
			$query .= implode(" AND ", $ands);
		}
		if (isset($filters['sort']) && $filters['sort'] != '') {
			$query .= " ORDER BY ".$filters['sort'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		
		return $query;
	}
	
	//-----------
	
	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r" . $this->buildQuery( $filters );
		
		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function getRecords( $filters=array() ) 
	{
		$query  = "SELECT * FROM $this->_tbl AS r" . $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}
