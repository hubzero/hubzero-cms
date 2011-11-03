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
 * Short description for 'Hubzero_Message_Recipient'
 * 
 * Long description (if any) ...
 */
class Hubzero_Message_Recipient extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'mid'
	 * 
	 * @var unknown
	 */
	var $mid      = NULL;  // @var int(11)

	/**
	 * Description for 'uid'
	 * 
	 * @var unknown
	 */
	var $uid      = NULL;  // @var int(11)

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created  = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'expires'
	 * 
	 * @var unknown
	 */
	var $expires  = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'actionid'
	 * 
	 * @var unknown
	 */
	var $actionid = NULL;  // @var int(11)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state    = NULL;  // @var tinyint(2)

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
		parent::__construct( '#__xmessage_recipient', 'id', $db );
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
		if (trim( $this->mid ) == '') {
			$this->setError( JText::_('Please provide a message ID.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadRecord'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $mid Parameter description (if any) ...
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadRecord( $mid=NULL, $uid=NULL )
	{
		if (!$mid) {
			$mid = $this->mid;
		}
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$mid || !$uid) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE mid='$mid' AND uid='$uid'" );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	private function buildQuery( $uid, $filters=array() )
	{
		$query  = "FROM #__xmessage AS m LEFT JOIN #__xmessage_seen AS s ON s.mid=m.id AND s.uid='$uid', $this->_tbl AS r 
					WHERE r.uid='$uid' 
					AND r.mid=m.id ";
		if (isset($filters['state'])) {
			$query .= "AND r.state='".$filters['state']."'";
		}
		if (isset($filters['filter']) && $filters['filter'] != '') {
			$query .= "AND m.component='".$filters['filter']."'";
		}
		if (isset($filters['limit']) && $filters['limit'] != 0) {
			$query .= " ORDER BY importance DESC, created DESC";
			$query .= " LIMIT ".$filters['start'].",".$filters['limit'];
		}
		return $query;
	}

	/**
	 * Short description for 'getMessages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getMessages( $uid=null, $filters=array() )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}

		$query = "SELECT m.*, s.whenseen, r.expires, r.actionid, r.state,
		 			(CASE WHEN r.actionid > 0 AND s.whenseen IS NULL THEN 1 ELSE 0 END) AS importance ".$this->buildQuery( $uid, $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getMessagesCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      array $filters Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getMessagesCount( $uid=null, $filters=array() )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}

		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) ".$this->buildQuery( $uid, $filters );

		$this->_db->setQuery( $query );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getUnreadMessages'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @param      unknown $limit Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getUnreadMessages( $uid=null, $limit=null )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}

		$query = "SELECT DISTINCT m.*, r.expires, r.actionid 
				FROM #__xmessage AS m, $this->_tbl AS r
				WHERE m.id = r.mid AND r.uid='$uid' AND m.id NOT IN (SELECT s.mid FROM #__xmessage_seen AS s WHERE s.uid='$uid')";
		$query .= " ORDER BY created DESC";
		$query .= ($limit) ? " LIMIT $limit" : "";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'deleteTrash'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $uid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteTrash( $uid=null )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}

		$query = "DELETE FROM $this->_tbl WHERE uid='$uid' AND state='2'";

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getError() );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'setState'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      integer $state Parameter description (if any) ...
	 * @param      array $ids Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function setState( $state=0, $ids=array() )
	{
		if (count($ids) <= 0) {
			return false;
		}

		$ids = implode(',',$ids);
		$query = "UPDATE $this->_tbl SET state='$state' WHERE id IN ($ids)";

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getError() );
			return false;
		}
		return true;
	}
}

