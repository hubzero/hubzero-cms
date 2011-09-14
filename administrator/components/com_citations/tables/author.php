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

	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r" . $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	public function getRecords( $filters=array() )
	{
		$query  = "SELECT * FROM $this->_tbl AS r" . $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

