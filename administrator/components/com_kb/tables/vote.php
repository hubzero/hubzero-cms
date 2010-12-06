<?php
/**
 * @package		HUBzero CMS
 * @author		Shawn Rice <zooley@purdue.edu>
 * @copyright	Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906
 * @license		http://www.gnu.org/licenses/gpl-2.0.html GPLv2
 *
 * Copyright 2005-2009 by Purdue Research Foundation, West Lafayette, IN 47906.
 * All rights reserved.
 *
 * This program is free software; you can redistribute it and/or
 * modify it under the terms of the GNU General Public License,
 * version 2 as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA  02110-1301, USA.
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
