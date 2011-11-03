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
 * Short description for 'WikiPageRevision'
 * 
 * Long description (if any) ...
 */
class WikiPageRevision extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'pageid'
	 * 
	 * @var unknown
	 */
	var $pageid     = NULL;  // @var int(11)

	/**
	 * Description for 'version'
	 * 
	 * @var unknown
	 */
	var $version    = NULL;  // @var int(11)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    = NULL;  // @var datetime

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by = NULL;  // @var int(11)

	/**
	 * Description for 'minor_edit'
	 * 
	 * @var unknown
	 */
	var $minor_edit = NULL;  // @var int(1)

	/**
	 * Description for 'pagetext'
	 * 
	 * @var unknown
	 */
	var $pagetext   = NULL;  // @var text

	/**
	 * Description for 'pagehtml'
	 * 
	 * @var unknown
	 */
	var $pagehtml   = NULL;  // @var text

	/**
	 * Description for 'approved'
	 * 
	 * @var unknown
	 */
	var $approved   = NULL;  // @var int(1)

	/**
	 * Description for 'summary'
	 * 
	 * @var unknown
	 */
	var $summary    = NULL;  // @var varchar(255)

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
		parent::__construct( '#__wiki_version', 'id', $db );
	}

	/**
	 * Short description for 'loadByVersion'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $pageid Parameter description (if any) ...
	 * @param      integer $version Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadByVersion( $pageid, $version=0 )
	{
		if (!$pageid) {
			return;
		}
		if ($version) {
			$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND version='$version'" );
		} else {
			$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC LIMIT 1" );
		}
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'getContributors'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     array Return description (if any) ...
	 */
	public function getContributors()
	{
		$this->_db->setQuery( "SELECT DISTINCT created_by AS id FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'" );
		$contributors = $this->_db->loadObjectList();

		$cons = array();
		if (count($contributors) > 0) {
			foreach ($contributors as $con)
			{
				$cons[] = $con->id;
			}
		}
		return $cons;
	}

	/**
	 * Short description for 'getRevisionCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getRevisionCount()
	{
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE pageid='$this->pageid' AND approved='1'" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRevisionNumbers'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $pageid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRevisionNumbers( $pageid=NULL )
	{
		if (!$pageid) {
			$pageid = $this->pageid;
		}
		$this->_db->setQuery( "SELECT DISTINCT version FROM $this->_tbl WHERE pageid='$pageid' AND approved='1' ORDER BY version DESC" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getRevisions'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $pageid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getRevisions( $pageid=NULL )
	{
		if (!$pageid) {
			$pageid = $this->pageid;
		}
		//$this->_db->setQuery( "SELECT id, pageid, version, created, created_by, minor_edit, approved, summary FROM $this->_tbl WHERE pageid='$pageid' ORDER BY version DESC, created DESC" );
		//return $this->_db->loadObjectList();
		return $this->getRecords( array('pageid'=>$pageid) );
	}

	/**
	 * Short description for 'getRecordsCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecordsCount( $filters=array() )
	{
		$sql  = "SELECT COUNT(*) ";
		$sql .= $this->buildQuery( $filters );

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
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
		$sql  = "SELECT r.id, r.pageid, r.version, r.created, r.created_by, r.minor_edit, r.approved, r.summary, u.name AS created_by_name, u.username AS created_by_alias ";
		$sql .= $this->buildQuery( $filters );

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters)
	{
		$query = " FROM $this->_tbl AS r,
		 			#__users AS u 
					WHERE r.created_by=u.id AND r.pageid='".$filters['pageid']."'";
		if (isset($filters['search']) && $filters['search']) {
			$query .= " AND LOWER( r.pagehtml ) LIKE '%".strtolower($filters['search'])."%'";
		}

		if (isset($filters['sortby']) && $filters['sortby'] != '') {
			$query .= " ORDER BY ".$filters['sortby'];
		} else {
			$query .= " ORDER BY version DESC, created DESC";
		}

		if (isset($filters['limit']) && $filters['limit'] != 0  && $filters['limit'] != 'all') {
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}

		return $query;
	}
}

