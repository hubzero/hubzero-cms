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
 * Table class for a forum category
 */
class ForumSection extends JTable
{
	/**
	 * Primary key
	 * 
	 * @var integer int(11) 
	 */
	var $id = NULL;

	/**
	 * Description for 'title'
	 * 
	 * @var string varchar(255)
	 */
	var $title = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var unknown
	 */
	var $alias      = NULL;

	/**
	 * datetime (0000-00-00 00:00:00)
	 * 
	 * @var string 
	 */
	var $created = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $created_by = NULL;

	/**
	 * int(2)
	 * Pushed state (0=unpublished, 1=published, 2=trashed)
	 * 
	 * @var integer 
	 */
	var $state = NULL;

	/**
	 * Group the entry belongs to
	 * 
	 * @var integer int(11)
	 */
	var $group_id = NULL;

	/**
	 * tinyint(2)
	 * Access level (0=public, 1=registered, 2=special, 3=protected, 4=private)
	 * 
	 * @var integer 
	 */
	var $access = NULL;

	/**
	 * int(11)
	 * ID for ACL asset (J1.6+)
	 * 
	 * @var integer 
	 */
	var $asset_id = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__forum_sections', 'id', $db);
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
		return 'com_forum.section.' . (int) $this->$k;
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
			$query->where('name = ' . $db->quote('com_forum'));

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
	 * Load a record by its alias and bind data to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True upon success, False if errors
	 */
	public function loadByAlias($oid=NULL, $group_id=null)
	{
		if ($oid === NULL) 
		{
			return false;
		}
		$oid = trim($oid);

		$query = "SELECT * FROM $this->_tbl WHERE alias='$oid'";
		if ($group_id !== null)
		{
			$query .= " AND group_id=" . $group_id;
		}

		$this->_db->setQuery($query);
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
	 * Populate the object with default data
	 * 
	 * @param      integer $group ID of group the data belongs to
	 * @return     boolean True if data is bound to $this object
	 */
	public function loadDefault($group=0)
	{
		$result = array(
			'id' => 0,
			'title' => JText::_('Categories'),
			'created_by' => 0,
			'group_id' => $group,
			'state' => 1,
			'access' => 1
		);
		$result['alias'] = str_replace(' ', '-', $result['title']);
		$result['alias'] = preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($result['alias']));

		return $this->bind($result);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		if (trim($this->title) == '') 
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
			$juser = JFactory::getUser();
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
			$this->state = 1;
		}

		return true;
	}

	/**
	 * Build a query based off of filters passed
	 * 
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	public function buildQuery($filters=array())
	{
		$query  = "FROM $this->_tbl AS c";
		$query .= " LEFT JOIN #__xgroups AS g ON g.gidNumber=c.group_id";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= " LEFT JOIN #__groups AS a ON c.access=a.id";
		}
		else 
		{
			$query .= " LEFT JOIN #__viewlevels AS a ON c.access=a.id";
		}

		$where = array();

		if (isset($filters['state'])) 
		{
			$where[] = "c.state=" . $this->_db->Quote($filters['state']);
		}

		if (isset($filters['group']) && $filters['group'] >= 0) 
		{
			$where[] = "c.group_id=" . $this->_db->Quote($filters['group']);
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "LOWER(c.title) LIKE '%" . strtolower($filters['search']) . "%'";
		}

		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
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

		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

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
		$query  = "SELECT c.*, g.cn AS group_alias";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= ", a.name AS access_level";
		}
		else 
		{
			$query .= ", a.title AS access_level";
		}
		$query .= " " . $this->buildQuery($filters);

		if (!isset($filters['sort']) || !$filters['sort']) 
		{
			$filters['sort'] = 'title';
		}
		if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
		{
			$filters['sort_Dir'] = 'ASC';
		}
		$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		
		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . $filters['start'] . ',' . $filters['limit'];
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the last post of a thread
	 * 
	 * @param      integer $parent Parent post (thread ID)
	 * @return     array
	 */
	public function getLastPost($parent=null)
	{
		if (!$parent) 
		{
			$parent = $this->parent;
		}
		if (!$parent) 
		{
			return null;
		}

		$query = "SELECT r.* FROM $this->_tbl AS r WHERE r.parent=$parent ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all replies to a parent entry
	 * 
	 * @param      integer $parent Parent post (thread ID)
	 * @return     boolean False if errors, True otherwise
	 */
	public function deleteReplies($parent=null)
	{
		if (!$parent) 
		{
			$parent = $this->parent;
		}
		if (!$parent) 
		{
			return null;
		}

		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE parent=$parent");
		if (!$this->_db->query()) 
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		} 
		else 
		{
			return true;
		}
	}
}
