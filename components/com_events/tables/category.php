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
defined('_JEXEC') or die('Restricted access');

/**
 * Events table class for category
 */
class EventsCategory extends JTable
{
	/**
	 * int(11) Primary Key
	 * 
	 * @var integer
	 */
	var $id               = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $parent_id        = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $title            = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $name             = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $alias            = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string
	 */
	var $image            = NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $section          = NULL;

	/**
	 * varchar(30)
	 * 
	 * @var string
	 */
	var $image_position   = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $description      = NULL;

	/**
	 * int(1)
	 * 
	 * @var string
	 */
	var $published        = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $checked_out      = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $checked_out_time = NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $editor           = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $ordering         = NULL;

	/**
	 * int(3)
	 * 
	 * @var string
	 */
	var $access           = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $count            = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $params           = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__categories', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->title) == '') 
		{
			$this->_error = JText::_('EVENTS_CATEGORY_MUST_HAVE_TITLE');
			return false;
		}
		return true;
	}

	/**
	 * Update the count of an entry
	 * 
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function updateCount($oid=NULL)
	{
		if ($oid == NULL) 
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET count = count-1 WHERE id=" . $this->_db->Quote($oid));
		$this->_db->query();
	}

	/**
	 * Set en entry to unpublished
	 * 
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function publish($oid=NULL)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=1 WHERE id=" . $this->_db->Quote($oid));
		$this->_db->query();
	}

	/**
	 * Set an entry to published
	 * 
	 * @param      integer $oid Category ID
	 * @return     void
	 */
	public function unpublish($oid=NULL)
	{
		if (!$oid) 
		{
			$oid = $this->id;
		}
		$this->_db->setQuery("UPDATE $this->_tbl SET published=0 WHERE id=" . $this->_db->Quote($oid));
		$this->_db->query();
	}

	/**
	 * Get a count of categories in a section
	 * 
	 * @param      integer $section Section ID
	 * @return     integer
	 */
	public function getCategoryCount($section=NULL)
	{
		if (!$section) 
		{
			$section = $this->section;
		}
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE section=" . $this->_db->Quote($section));
		}
		else
		{
			$this->_db->setQuery("SELECT COUNT(*) FROM $this->_tbl WHERE extension=" . $this->_db->Quote($section));
		}
		return $this->_db->loadResult();
	}
}

