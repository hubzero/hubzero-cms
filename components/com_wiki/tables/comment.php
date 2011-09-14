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
 * Short description for 'WikiPageComment'
 * 
 * Long description (if any) ...
 */
class WikiPageComment extends JTable
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
	 * Description for 'ctext'
	 * 
	 * @var unknown
	 */
	var $ctext      = NULL;  // @var text


	/**
	 * Description for 'chtml'
	 * 
	 * @var unknown
	 */
	var $chtml      = NULL;  // @var text


	/**
	 * Description for 'rating'
	 * 
	 * @var unknown
	 */
	var $rating     = NULL;  // @var int(1)


	/**
	 * Description for 'anonymous'
	 * 
	 * @var unknown
	 */
	var $anonymous  = NULL;  // @var int(1)


	/**
	 * Description for 'parent'
	 * 
	 * @var unknown
	 */
	var $parent     = NULL;  // @var int(11)


	/**
	 * Description for 'status'
	 * 
	 * @var unknown
	 */
	var $status     = NULL;  // @var int(1)

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
		parent::__construct( '#__wiki_comments', 'id', $db );
	}

	/**
	 * Short description for 'getResponses'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     object Return description (if any) ...
	 */
	public function getResponses()
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE parent='$this->id'" );
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'report'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function report( $oid=null )
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}

		$this->_db->setQuery( "UPDATE $this->_tbl SET status=1 WHERE $this->_tbl_key = '".$this->$k."'" );

		if ($this->_db->query()) {
			return true;
		} else {
			$this->_error = $this->_db->getErrorMsg();
			return false;
		}
	}

	/**
	 * Short description for 'getComments'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      string $id Parameter description (if any) ...
	 * @param      string $parent Parameter description (if any) ...
	 * @param      string $ver Parameter description (if any) ...
	 * @param      string $limit Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getComments( $id, $parent, $ver='', $limit='' )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='".$id."' AND parent=".$parent." $ver ORDER BY created DESC $limit" );
		return $this->_db->loadObjectList();
	}
}

