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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------


/**
 * Short description for 'XGroupLog'
 * 
 * Long description (if any) ...
 */
class XGroupLog extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id        = NULL;  // @var int(11) Primary key


	/**
	 * Description for 'gid'
	 * 
	 * @var unknown
	 */
	var $gid       = NULL;  // @var int(11)


	/**
	 * Description for 'timestamp'
	 * 
	 * @var unknown
	 */
	var $timestamp = NULL;  // @var datetime(0000-00-00 00:00:00)


	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid       = NULL;  // @var int(11)


	/**
	 * Description for 'action'
	 * 
	 * @var unknown
	 */
	var $action    = NULL;  // @var varchar(50)


	/**
	 * Description for 'comments'
	 * 
	 * @var unknown
	 */
	var $comments  = NULL;  // @var text


	/**
	 * Description for 'actorid'
	 * 
	 * @var unknown
	 */
	var $actorid   = NULL;  // @var int(11)

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
		parent::__construct( '#__xgroups_log', 'id', $db );
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
		if (trim( $this->gid ) == '') {
			$this->setError( JText::_('GROUPS_LOGS_MUST_HAVE_GROUP_ID') );
			return false;
		}

		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('GROUPS_LOGS_MUST_HAVE_USER_ID') );
			return false;
		}

		return true;
	}

	/**
	 * Short description for 'getLogs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      mixed $limit Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLogs( $gid=null, $limit=5 )
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ORDER BY `timestamp` DESC";
		if ($limit) {
			$query .= " LIMIT ".$limit;
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLog'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      string $which Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getLog( $gid=null, $which='first' )
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}

		$query = "SELECT * FROM $this->_tbl WHERE gid=$gid ";
		if ($which == 'first') {
			$query .= "ORDER BY `timestamp` ASC LIMIT 1";
		} else {
			$query .= "ORDER BY `timestamp` DESC LIMIT 1";
		}

		$this->_db->setQuery( $query );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'deleteLogs'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $gid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteLogs( $gid=null )
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}

		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE gid=".$gid );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'logCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $gid Parameter description (if any) ...
	 * @param      string $action Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function logCount( $gid=null, $action='' )
	{
		if (!$gid) {
			$gid = $this->gid;
		}
		if (!$gid) {
			return null;
		}

		$query = "SELECT COUNT(*) FROM $this->_tbl WHERE gid=$gid";
		if ($action) {
			$query .= " AND action='$action'";
		}

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}
}

