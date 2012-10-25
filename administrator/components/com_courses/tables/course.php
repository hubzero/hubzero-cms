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
 * Courses table
 */
class CoursesTableCourse extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer
	 */
	var $id = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $alias    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $group_id = NULL;

	/**
	 * varchar(50)
	 * 
	 * @var string
	 */
	var $title = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $state = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $type = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $access = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $public_desc = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $private_desc = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $restrict_msg = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $join_policy = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $privacy = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $discussion_email_autosubscribe = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $logo = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $overview_type = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $overview_content = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $plugins = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string
	 */
	var $created_by = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $params = NULL;

	/**
	 * Constructor
	 * 
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__courses', 'id', $db);
	}

	/**
	 * Method to compute the default name of the asset.
	 * The default name is in the form table_name.id
	 * where id is the value of the primary key of the table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_courses.course.' . (int) $this->$k;
	}

	/**
	 * Method to return the title to use for the asset table.
	 *
	 * @return  string
	 *
	 * @since   11.1
	 */
	protected function _getAssetTitle()
	{
		return $this->title;
	}

	/**
	 * Get the parent asset id for the record
	 *
	 * @param   JTable   $table  A JTable object for the asset parent.
	 * @param   integer  $id     The id for the asset
	 *
	 * @return  integer  The id of the asset's parent
	 *
	 * @since   11.1
	 */
	protected function _getAssetParentId($table = null, $id = null)
	{
		// Initialise variables.
		$assetId = null;
		$db = $this->getDbo();

		if ($assetId === null) 
		{
			// Build the query to get the asset id for the parent category.
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = ' . $db->quote('com_courses'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult()) 
			{
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId) 
		{
			return $assetId;
		} 
		else 
		{
			return parent::_getAssetParentId($table, $id);
		}
	}

	/**
	 * Validate fields before store()
	 * 
	 * @return     boolean True if all fields are valid
	 */
	public function check()
	{
		if (trim($this->cn) == '') 
		{
			$this->setError(JText::_('COM_COURSES_ERROR_EMPTY_TITLE'));
			return false;
		}
		return true;
	}

	/**
	 * Save changes
	 * 
	 * @return     boolean
	 */
	public function save()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}
	
	/**
	 * Insert or Update the object
	 * 
	 * @return     boolean
	 */
	public function store()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}

	/**
	 * Populate the current object with a database record if found
	 * Accepts either an alias or an ID
	 * 
	 * @param      mixed $oid Unique ID or alias of object to retrieve
	 * @return     boolean True on success
	 */
	public function load($oid=NULL)
	{
		if (empty($oid)) 
		{
			return false;
		}

		if (is_numeric($oid)) 
		{
			return parent::load($oid);
		}

		$sql  = "SELECT * FROM $this->_tbl WHERE `alias`='$oid' LIMIT 1";
		$this->_db->setQuery($sql);
		if ($result = $this->_db->loadAssoc()) 
		{
			return $this->bind($result);
		} 
		else 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
	}
}

