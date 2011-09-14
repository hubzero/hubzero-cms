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

class WikiPageComment extends JTable
{
	var $id         = NULL;  // @var int(11) Primary key
	var $pageid     = NULL;  // @var int(11)
	var $version    = NULL;  // @var int(11)
	var $created    = NULL;  // @var datetime
	var $created_by = NULL;  // @var int(11)
	var $ctext      = NULL;  // @var text
	var $chtml      = NULL;  // @var text
	var $rating     = NULL;  // @var int(1)
	var $anonymous  = NULL;  // @var int(1)
	var $parent     = NULL;  // @var int(11)
	var $status     = NULL;  // @var int(1)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__wiki_comments', 'id', $db );
	}

	public function getResponses()
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE parent='$this->id'" );
		return $this->_db->loadObjectList();
	}

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

	public function getComments( $id, $parent, $ver='', $limit='' )
	{
		$this->_db->setQuery( "SELECT * FROM $this->_tbl WHERE pageid='".$id."' AND parent=".$parent." $ver ORDER BY created DESC $limit" );
		return $this->_db->loadObjectList();
	}
}

