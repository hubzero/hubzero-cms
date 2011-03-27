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


class KbVote extends JTable 
{
	var $id        = NULL;  // @var int(11) Primary key
	var $object_id = NULL;  // @var int(11)
	var $ip        = NULL;  // @var varchar(15)
	var $vote      = NULL;  // @var varchar(10)
	var $user_id   = NULL;  // @var int(11)
	//var $voted     = NULL;  // @var datetime(0000-00-00 00:00:00)
	var $type      = NULL;  // @var varchar(255)
	
	//-----------
	
	public function __construct( &$db )
	{
		parent::__construct( '#__faq_helpful_log', 'id', $db );
	}
	
	//-----------
	
	public function check() 
	{
		if (trim( $this->object_id ) == '') {
			$this->setError( JText::_('COM_KB_ERROR_MISSING_ARTICLE_ID') );
			return false;
		}
		return true;
	}
	
	//-----------
	
	public function getVote( $object_id=NULL, $user_id=NULL, $ip=NULL, $type=NULL )
	{
		if ($object_id == NULL) {
			$object_id = $this->object_id;
		}
		if ($user_id == NULL) {
			$user_id = $this->user_id;
		}
		if ($ip == NULL) {
			$ip = $this->ip;
		}
		if ($type == NULL) {
			$type = $this->type;
		}
		$this->_db->setQuery( "SELECT vote FROM $this->_tbl WHERE object_id='$object_id' AND (user_id='$user_id' OR ip='$ip') AND type='$type'" );
		return $this->_db->loadResult();
	}
	
	//-----------
	
	public function deleteVote( $object_id=NULL, $user_id=NULL ) 
	{
		if ($object_id == NULL) {
			$object_id = $this->object_id;
		}
		if ($user_id == NULL) {
			$user_id = $this->user_id;
		}
		
		$sql = "DELETE FROM $this->_tbl WHERE object_id='$object_id'";
		$sql .= ($user_id) ? " AND user_id='$user_id'" : "";
		
		$this->_db->setQuery( $sql );
		if ($this->_db->query()) {
			return true;
		} else {
			$this->setError( $this->_db->getErrorMsg() );
			return false;
		}
	}
}

