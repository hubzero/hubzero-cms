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
