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

//----------------------------------------------------------
// Extended database class
//----------------------------------------------------------

class XGroupLog extends JTable
{
	var $id        = NULL;  // @var int(11) Primary key
	var $gid       = NULL;  // @var int(11)
	var $timestamp = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $uid       = NULL;  // @var int(11)
	var $action    = NULL;  // @var varchar(50)
	var $comments  = NULL;  // @var text
	var $actorid   = NULL;  // @var int(11)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__xgroups_log', 'id', $db );
	}

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

