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
 * Short description for 'TagsObject'
 * 
 * Long description (if any) ...
 */
class TagsObject extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id       = NULL;  // int(11)


	/**
	 * Description for 'objectid'
	 * 
	 * @var unknown
	 */
	var $objectid = NULL;  // int(11)


	/**
	 * Description for 'tagid'
	 * 
	 * @var unknown
	 */
	var $tagid    = NULL;  // int(11)


	/**
	 * Description for 'strength'
	 * 
	 * @var unknown
	 */
	var $strength = NULL;  // tinyint(3)


	/**
	 * Description for 'taggerid'
	 * 
	 * @var unknown
	 */
	var $taggerid = NULL;  // int(11)


	/**
	 * Description for 'taggedon'
	 * 
	 * @var unknown
	 */
	var $taggedon = NULL;  // datetime(0000-00-00 00:00:00)


	/**
	 * Description for 'tbl'
	 * 
	 * @var unknown
	 */
	var $tbl      = NULL;  // varchar(255)

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
		parent::__construct( '#__tags_object', 'id', $db );
	}

	/**
	 * Short description for 'deleteObjects'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tagid Parameter description (if any) ...
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @param      unknown $objectid Parameter description (if any) ...
	 * @param      unknown $taggerid Parameter description (if any) ...
	 * @param      boolean $admin Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function deleteObjects( $tagid=null, $tbl=null, $objectid=null, $taggerid=null, $admin=false )
	{
		if (!$tagid) {
			$tagid = $this->tagid;
		}
		if (!$tagid) {
			return false;
		}

		$sql = "DELETE FROM $this->_tbl WHERE tagid='$tagid'";
		if ($tbl) {
			$sql .= " AND tbl='$tbl'";
		}
		if ($objectid) {
			$sql .= " AND objectid='$objectid'";
		}
		if (!$admin) {
			$sql .= " AND taggerid='$taggerid'";
		}

		$this->_db->setQuery( $sql );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'removeAllTags'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @param      unknown $objectid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function removeAllTags( $tbl=null, $objectid=null )
	{
		if (!$tbl) {
			$tbl = $this->tbl;
		}
		if (!$objectid) {
			$objectid = $this->objectid;
		}
		if (!$tbl || !$objectid) {
			return false;
		}

		$sql = "DELETE FROM $this->_tbl WHERE tbl='$tbl' AND objectid='$objectid'";

		$this->_db->setQuery( $sql );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tagid Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getCount( $tagid=null )
	{
		if (!$tagid) {
			$tagid = $this->tagid;
		}
		if (!$tagid) {
			return false;
		}

		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE tagid='$tagid'" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getTagsOnObject'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $objectid Parameter description (if any) ...
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @param      integer $state Parameter description (if any) ...
	 * @param      integer $offset Parameter description (if any) ...
	 * @param      integer $limit Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getTagsOnObject($objectid=null, $tbl=null, $state=0, $offset=0, $limit=10)
	{
		if (!$objectid) {
			$objectid = $this->objectid;
		}
		if (!$tbl) {
			$tbl = $this->tbl;
		}
		if (!$tbl || !$objectid) {
			$this->setError( JText::_('Missing argument.') );
			return false;
		}

		$sql = "SELECT DISTINCT t.* 
				FROM $this->_tbl AS rt 
				INNER JOIN #__tags AS t ON (rt.tagid = t.id)
				WHERE rt.objectid='$objectid' AND rt.tbl='$tbl'";
		switch ($state)
		{
			case 0: $sql .= " AND t.admin=0"; break;
			case 1: $sql .= ""; break;
		}
		$sql .= " ORDER BY t.raw_tag ASC";
		if ($limit > 0) {
			$sql .= " LIMIT $offset, $limit";
		}

		$this->_db->setQuery( $sql );
		return $this->_db->loadAssocList();
	}

	/**
	 * Short description for 'getCountForObject'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $tagid Parameter description (if any) ...
	 * @param      unknown $objectid Parameter description (if any) ...
	 * @param      unknown $tbl Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function getCountForObject( $tagid=null, $objectid=null, $tbl=null )
	{
		if (!$tagid) {
			$tagid = $this->tagid;
		}
		if (!$objectid) {
			$objectid = $this->objectid;
		}
		if (!$tbl) {
			$tbl = $this->tbl;
		}
		if (!$tagid || !$tbl || !$objectid) {
			return false;
		}

		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE tagid='$tagid' AND objectid='$objectid' AND tbl='$tbl'" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'moveObjects'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oldtagid Parameter description (if any) ...
	 * @param      unknown $newtagid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function moveObjects( $oldtagid=null, $newtagid=null )
	{
		if (!$oldtagid) {
			$oldtagid = $this->tagid;
		}
		if (!$oldtagid) {
			return false;
		}
		if (!$newtagid) {
			return false;
		}

		$this->_db->setQuery( "UPDATE $this->_tbl SET tagid='$newtagid' WHERE tagid='$oldtagid'" );
		if (!$this->_db->query()) {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'copyObjects'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oldtagid Parameter description (if any) ...
	 * @param      unknown $newtagid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function copyObjects( $oldtagid=null, $newtagid=null )
	{
		if (!$oldtagid) {
			$oldtagid = $this->tagid;
		}
		if (!$oldtagid) {
			return false;
		}
		if (!$newtagid) {
			return false;
		}

		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE tagid='$oldtagid'" );
		$rows = $this->_db->loadObjectList();
		if ($rows) {
			foreach ($rows as $row)
			{
				$to = new TagsObject($this->_db);
				$to->objectid = $row->objectid;
				$to->tagid    = $newtagid;
				$to->strength = $row->strength;
				$to->taggerid = $row->taggerid;
				$to->taggedon = $row->taggedon;
				$to->tbl = $row->tbl;
				$to->store();
			}
		}
		return true;
	}
}

