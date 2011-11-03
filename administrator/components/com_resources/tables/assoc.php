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
 * Short description for 'ResourcesAssoc'
 * 
 * Long description (if any) ...
 */
class ResourcesAssoc extends JTable
{

	/**
	 * Description for 'parent_id'
	 * 
	 * @var string
	 */
	var $parent_id = NULL;  // @var int(11)

	/**
	 * Description for 'child_id'
	 * 
	 * @var string
	 */
	var $child_id  = NULL;  // @var int(11)

	/**
	 * Description for 'ordering'
	 * 
	 * @var string
	 */
	var $ordering  = NULL;  // @var int(11)

	/**
	 * Description for 'grouping'
	 * 
	 * @var string
	 */
	var $grouping  = NULL;  // @var int(11)

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
		parent::__construct( '#__resource_assoc', 'parent_id', $db );
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
		if (trim( $this->child_id ) == '') {
			$this->setError( JText::_('Your resource association must have a child.') );
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'loadAssoc'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pid Parameter description (if any) ...
	 * @param      string $cid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadAssoc( $pid, $cid )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE parent_id=".$pid." AND child_id=".$cid );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'getNeighbor'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $move Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function getNeighbor( $move )
	{
		switch ($move)
		{
			case 'orderup':
				$sql = "SELECT * FROM $this->_tbl WHERE parent_id=".$this->parent_id." AND ordering < ".$this->ordering." ORDER BY ordering DESC LIMIT 1";
				break;

			case 'orderdown':
				$sql = "SELECT * FROM $this->_tbl WHERE parent_id=".$this->parent_id." AND ordering > ".$this->ordering." ORDER BY ordering LIMIT 1";
				break;
		}
		$this->_db->setQuery( $sql );
		//return $this->_db->loadObject( $this );
		if ($result = $this->_db->loadAssoc()) {
			return $this->bind( $result );
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}

	/**
	 * Short description for 'getLastOrder'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLastOrder( $pid=NULL )
	{
		if (!$pid) {
			$pid = $this->parent_id;
		}
		$this->_db->setQuery( "SELECT ordering FROM $this->_tbl WHERE parent_id=".$pid." ORDER BY ordering DESC LIMIT 1" );
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'delete'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pid Parameter description (if any) ...
	 * @param      string $cid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function delete( $pid=NULL, $cid=NULL )
	{
		if (!$pid) {
			$pid = $this->parent_id;
		}
		if (!$cid) {
			$cid = $this->child_id;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE parent_id=".$pid." AND child_id=".$cid );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	/**
	 * Short description for 'store'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      boolean $new Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function store( $new=false )
	{
		if (!$new) {
			$this->_db->setQuery( "UPDATE $this->_tbl SET ordering=".$this->ordering.", grouping=".$this->grouping." WHERE child_id=".$this->child_id." AND parent_id=".$this->parent_id);
			if ($this->_db->query()) {
				$ret = true;
			} else {
				$ret = false;
			}
		} else {
			$ret = $this->_db->insertObject( $this->_tbl, $this, $this->_tbl_key );
		}
		if (!$ret) {
			$this->setError( strtolower(get_class( $this )).'::store failed <br />' . $this->_db->getErrorMsg() );
			return false;
		} else {
			return true;
		}
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $pid Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount( $pid=NULL )
	{
		if (!$pid) {
			$pid = $this->parent_id;
		}
		if (!$pid) {
			return null;
		}
		$this->_db->setQuery( "SELECT count(*) FROM $this->_tbl WHERE parent_id=".$pid );
		return $this->_db->loadResult();
	}
}

