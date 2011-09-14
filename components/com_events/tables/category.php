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
 * Short description for 'EventsCategory'
 * 
 * Long description (if any) ...
 */
class EventsCategory extends JTable
{

	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id               = NULL;

	/**
	 * Description for 'parent_id'
	 * 
	 * @var unknown
	 */
	var $parent_id        = NULL;

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title            = NULL;

	/**
	 * Description for 'name'
	 * 
	 * @var unknown
	 */
	var $name             = NULL;

	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias            = NULL;

	/**
	 * Description for 'image'
	 * 
	 * @var unknown
	 */
	var $image            = NULL;

	/**
	 * Description for 'section'
	 * 
	 * @var unknown
	 */
	var $section          = NULL;

	/**
	 * Description for 'image_position'
	 * 
	 * @var unknown
	 */
	var $image_position   = NULL;

	/**
	 * Description for 'description'
	 * 
	 * @var unknown
	 */
	var $description      = NULL;

	/**
	 * Description for 'published'
	 * 
	 * @var unknown
	 */
	var $published        = NULL;

	/**
	 * Description for 'checked_out'
	 * 
	 * @var unknown
	 */
	var $checked_out      = NULL;

	/**
	 * Description for 'checked_out_time'
	 * 
	 * @var unknown
	 */
	var $checked_out_time = NULL;

	/**
	 * Description for 'editor'
	 * 
	 * @var unknown
	 */
	var $editor           = NULL;

	/**
	 * Description for 'ordering'
	 * 
	 * @var unknown
	 */
	var $ordering         = NULL;

	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access           = NULL;

	/**
	 * Description for 'count'
	 * 
	 * @var unknown
	 */
	var $count            = NULL;

	/**
	 * Description for 'params'
	 * 
	 * @var unknown
	 */
	var $params           = NULL;

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
		parent::__construct( '#__categories', 'id', $db );
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
		// check for valid name
		if (trim( $this->title ) == '') {
			$this->_error = JText::_('EVENTS_CATEGORY_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}

	/**
	 * Short description for 'updateCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     void
	 */
	public function updateCount( $oid=NULL )
	{
		if ($oid == NULL) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET count = count-1 WHERE id = '$oid'" );
		$this->_db->query();
	}

	/**
	 * Short description for 'publish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     void
	 */
	public function publish( $oid=NULL )
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET published=1 WHERE id=$oid" );
		$this->_db->query();
	}

	/**
	 * Short description for 'unpublish'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     void
	 */
	public function unpublish( $oid=NULL )
	{
		if (!$oid) {
			$oid = $this->id;
		}
		$this->_db->setQuery( "UPDATE $this->_tbl SET published=0 WHERE id=$oid" );
		$this->_db->query();
	}

	/**
	 * Short description for 'getCategoryCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $section Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCategoryCount( $section=NULL )
	{
		if (!$section) {
			$section = $this->section;
		}
		$this->_db->setQuery( "SELECT COUNT(*) FROM $this->_tbl WHERE section='$section'" );
		return $this->_db->loadResult();
	}
}

