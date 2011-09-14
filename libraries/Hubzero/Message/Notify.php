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

class Hubzero_Message_Notify extends JTable
{
	var $id       = NULL;  // @var int(11) Primary key
	var $uid      = NULL;  // @var int(11)
	var $method   = NULL;  // @var text
	var $type     = NULL;  // @var text
	var $priority = NULL;  // @var int(11)

	//-----------

	public function __construct( &$db )
	{
		parent::__construct( '#__xmessage_notify', 'id', $db );
	}

	public function check()
	{
		if (trim( $this->uid ) == '') {
			$this->setError( JText::_('Please provide a user ID.') );
			return false;
		}
		return true;
	}

	public function getRecords( $uid=null, $type=null )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}
		if (!$type) {
			$type = $this->type;
		}

		$query  = "SELECT * FROM $this->_tbl WHERE `uid`='$uid'";
		$query .= ($type) ? " AND `type`='$type'" : "";
		$query .= " ORDER BY `priority` ASC";

		$this->_db->setQuery( $query );
		return $this->_db->loadObjectList();
	}

	public function clearAll( $uid=null )
	{
		if (!$uid) {
			$uid = $this->uid;
		}
		if (!$uid) {
			return false;
		}

		$query  = "DELETE FROM $this->_tbl WHERE `uid`='$uid'";

		$this->_db->setQuery( $query );
		if (!$this->_db->query()) {
			return false;
		}
	}
}

