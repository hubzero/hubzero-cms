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
 * Short description for 'GroupsReason'
 * 
 * Long description (if any) ...
 */
class GroupsReason extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'uidNumber'
	 * 
	 * @var unknown
	 */
	var $uidNumber = NULL;  // @var int(11)

	/**
	 * Description for 'gidNumber'
	 * 
	 * @var unknown
	 */
	var $gidNumber      = NULL;  // @var int(11)

	/**
	 * Description for 'reason'
	 * 
	 * @var unknown
	 */
	var $reason   = NULL;  // @var text

	/**
	 * Description for 'date'
	 * 
	 * @var unknown
	 */
	var $date     = NULL;  // @var datetime

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
		parent::__construct( '#__xgroups_reasons', 'id', $db );
	}

	/**
	 * Short description for 'loadReason'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      unknown $gid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadReason( $uid, $gid )
	{
		if ($uid === NULL || $gid === NULL) {
			return false;
		}
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid' LIMIT 1" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'deleteReason'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      unknown $gid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteReason( $uid, $gid )
	{
		if ($uid === NULL || $gid === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE uidNumber='$uid' AND gidNumber='$gid'" );
		if (!$this->_db->query()) {
			$err = $this->_db->getErrorMsg();
			die( $err );
		}
		return true;
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
		if (trim( $this->gidNumber ) == '') {
			$this->setError( JText::_('GROUPS_REASON_MUST_HAVE_GROUPID') );
			return false;
		}

		if (trim( $this->uidNumber ) == '') {
			$this->setError( JText::_('GROUPS_REASON_MUST_HAVE_USERNAME') );
			return false;
		}

		return true;
	}
}

