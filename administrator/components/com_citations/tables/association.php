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

/**
 * Short description for 'CitationsAssociation'
 * 
 * Long description (if any) ...
 */
class CitationsAssociation extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id    = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'cid'
	 * 
	 * @var unknown
	 */
	var $cid   = NULL;  // @var int(11)


	/**
	 * Description for 'oid'
	 * 
	 * @var unknown
	 */
	var $oid   = NULL;  // @var varchar(200)


	/**
	 * Description for 'type'
	 * 
	 * @var unknown
	 */
	var $type  = NULL;  // @var int(3)


	/**
	 * Description for 'table'
	 * 
	 * @var unknown
	 */
	var $table = NULL;  // @var int(3)

	//-----------


	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct( &$db )
	{
		parent::__construct( '#__citations_assoc', 'id', $db );
	}

	/**
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
	 */
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

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
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

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $filters=array() )
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r" . $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords( $filters=array() )
	{
		$query  = "SELECT * FROM $this->_tbl AS r" . $this->buildQuery( $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}
}

