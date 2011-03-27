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

class EventsCategory extends JTable 
{
	var $id               = NULL;
	var $parent_id        = NULL;
	var $title            = NULL;
	var $name             = NULL;
	var $alias            = NULL;
	var $image            = NULL;
	var $section          = NULL;
	var $image_position   = NULL;
	var $description      = NULL;
	var $published        = NULL;
	var $checked_out      = NULL;
	var $checked_out_time = NULL;
	var $editor           = NULL;
	var $ordering         = NULL;
	var $access           = NULL;
	var $count            = NULL;
	var $params           = NULL;

	//-----------

	public function __construct( &$db ) 
	{
		parent::__construct( '#__categories', 'id', $db );
	}

	//-----------
	
	public function check() 
	{
		// check for valid name
		if (trim( $this->title ) == '') {
			$this->_error = JText::_('EVENTS_CATEGORY_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}

	//-----------
	
	public function updateCount( $oid=NULL ) 
	{
		if ($oid == NULL) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET count = count-1 WHERE id = '$oid'" );
		$this->_db->query();
	}
	
	//-----------
	
	public function publish( $oid=NULL ) 
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET published=1 WHERE id=$oid" );
		$this->_db->query();
	}

	//-----------

	public function unpublish( $oid=NULL ) 
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET published=0 WHERE id=$oid" );
		$this->_db->query();
	}
	
	//-----------
	
	public function getCategoryCount( $section=NULL ) 
	{
		if (!$section) {
			$section = $this->section;
		}
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE section='$section'" );
		return $this->_db->loadResult();
	}
}

