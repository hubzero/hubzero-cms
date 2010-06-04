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


class CitationsAuthor extends JTable 
{
	var $id              = NULL;  // @var int(11) Primary key
	var $cid             = NULL;  // @var int(11)
	var $author          = NULL;  // @var varchar(64)
	var $author_uid      = NULL;  // @var int(20)
	var $ordering        = NULL;  // @var int(11)
	var $givenName       = NULL;  // @var varchar(255)
	var $middleName      = NULL;  // @var varchar(255)
	var $surname         = NULL;  // @var varchar(255)
	var $organization    = NULL;  // @var varchar(255)
	var $org_dept        = NULL;  // @var varchar(255)
	var $orgtype         = NULL;  // @var varchar(255)
	var $countryresident = NULL;  // @var char(2)
	var $email           = NULL;  // @var varchar(100)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__citations_authors', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->cid ) == '') {
			$this->setError( JText::_('AUTHOR_MUST_HAVE_CITATION_ID') );
			return false;
		}
		if (trim( $this->author ) == '') {
			$this->setError( JText::_('AUTHOR_MUST_HAVE_TEXT') );
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
		if (isset($filters['author_uid']) && $filters['author_uid'] != 0) {
			$ands[] = "r.author_uid='".$filters['author_uid']."'";
		}
		if (isset($filters['author']) && trim($filters['author']) != '') {
			$ands[] = "LOWER(r.author)='".strtolower($filters['author'])."'";
		}
		if (count($ands) > 0) {
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
