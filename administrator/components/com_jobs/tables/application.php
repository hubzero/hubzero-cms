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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Short description for 'JobApplication'
 * 
 * Long description (if any) ...
 */
class JobApplication extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'jid'
	 * 
	 * @var unknown
	 */
	var $jid		= NULL;  // @var int(11)

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid		= NULL;  // @var int(11)

	/**
	 * Description for 'applied'
	 * 
	 * @var unknown
	 */
	var $applied	= NULL;

	/**
	 * Description for 'withdrawn'
	 * 
	 * @var unknown
	 */
	var $withdrawn	= NULL;

	/**
	 * Description for 'cover'
	 * 
	 * @var unknown
	 */
	var $cover		= NULL;

	/**
	 * Description for 'resumeid'
	 * 
	 * @var unknown
	 */
	var $resumeid	= NULL;

	/**
	 * Description for 'status'
	 * 
	 * @var unknown
	 */
	var $status		= NULL;

	/**
	 * Description for 'reason'
	 * 
	 * @var unknown
	 */
	var $reason		= NULL;

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
		parent::__construct( '#__jobs_applications', 'id', $db );
	}

	/**
	 * Short description for 'getApplications'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $jobid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getApplications ($jobid)
	{
		if ($jobid === NULL) {
			return false;
		}

		$sql = "SELECT a.* FROM  #__jobs_applications AS a ";
		$sql.= "JOIN #__jobs_seekers as s ON s.uid=a.uid";
		$sql.= "\n WHERE  a.jid='$jobid' AND s.active=1 ";
		$sql.= " ORDER BY a.applied DESC";

		$this->_db->setQuery( $sql );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'loadApplication'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      string $jid Parameter description (if any) ...
	 * @param      unknown $jobcode Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadApplication( $uid = NULL, $jid = NULL, $jobcode = NULL )
	{
		if ($uid === NULL or ($jid === NULL && $jobcode === NULL)) {
			return false;
		}

		$query  = "SELECT A.* FROM $this->_tbl as A ";
		$query .= $jid ? "" : " JOIN #__jobs_openings as J ON J.id=A.jid ";
		$query .= " WHERE A.uid='$uid' ";
		$query .=  $jid ? "AND A.jid='$jid' " : "AND J.code='$jobcode' ";
		$query .= " LIMIT 1";
		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			return false;
		}
	}
}

