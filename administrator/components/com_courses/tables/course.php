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
	var $blurb = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $description = NULL;

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
		$this->title = trim($this->title);

		if (!$this->title) 
		{
			$this->setError(JText::_('Please provide a title.'));
			return false;
		}

		if (!$this->alias)
		{
			$this->alias = str_replace(' ', '-', strtolower($this->title));
		}
		$this->alias = preg_replace("/[^a-zA-Z0-9\-]/", '', $this->alias);

		if (!$this->id)
		{
			$juser =& JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}
		return true;
	}

	/**
	 * Save changes
	 * 
	 * @return     boolean
	 */
	/*public function save()
	{
		$this->setError('You\'re doing it wrong!');
		return false;
	}
	
	/**
	 * Insert or Update the object
	 * 
	 * @return     boolean
	 */
	/*public function store()
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

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	protected function _buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS c";

		$where = array();

		if (isset($filters['state'])) 
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.title) LIKE '%" . strtolower($filters['search']) . "%' 
					OR LOWER(c.alias) LIKE '%" . strtolower($filters['search']) . "%')";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			if (!isset($filters['sort']) || !$filters['sort']) 
			{
				$filters['sort'] = 'title';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
			{
				$filters['sort_Dir'] = 'DESC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		}

		return $query;
	}

	/**
	 * Get a record count
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     array
	 */
	public function getRecords($filters=array())
	{
		$query = "SELECT c.*" . $this->_buildQuery($filters);

		if ($filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}

