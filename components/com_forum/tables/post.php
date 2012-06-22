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
 * Table class for forum posts
 */
class ForumPost extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer 
	 */
	var $id         = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $category_id = NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string  
	 */
	var $title      = NULL;

	/**
	 * text
	 * 
	 * @var string  
	 */
	var $comment    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string  
	 */
	var $created    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $created_by = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 * 
	 * @var string  
	 */
	var $modified   = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $modified_by = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $state      = NULL;

	/**
	 * int(2)
	 * 
	 * @var integer
	 */
	var $sticky     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $parent     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $hits       = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $group_id = NULL;

	/**
	 * tinyint(2)  0=public, 1=registered, 2=special, 3=protected, 4=private
	 * 
	 * @var integer
	 */
	var $access     = NULL;

	/**
	 * tinyint(2)
	 * 
	 * @var integer
	 */
	var $anonymous  = NULL;
	
	/**
	 * ID for ACL asset (J1.6+)
	 * 
	 * @var int(11)
	 */
	var $last_activity = null;
	
	/**
	 * ID for ACL asset (J1.6+)
	 * 
	 * @var int(11)
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
		parent::__construct('#__forum_posts', 'id', $db);
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
		$type = ($this->parent) ? 'post' : 'thread';
		return 'com_forum.' . $type . '.' . (int) $this->$k;
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
		$db		= $this->getDbo();

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
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->comment = trim($this->comment);

		if (!$this->comment) 
		{
			$this->setError(JText::_('Please provide a comment'));
			return false;
		}

		if (!$this->title) 
		{
			$this->title = substr($this->comment, 0, 70);
			if (strlen($this->title >= 70)) 
			{
				$this->title .= '...';
			}
		}

		$juser =& JFactory::getUser();
		if (!$this->id) 
		{
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->created_by = $juser->get('id');
		} 
		else 
		{
			$this->modified = date('Y-m-d H:i:s', time());  // use gmdate() ?
			$this->modified_by = $juser->get('id');
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
		$query  = " FROM $this->_tbl AS c";
		$query .= " LEFT JOIN #__xgroups AS g ON g.gidNumber=c.group_id";
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= " LEFT JOIN #__groups AS a ON c.access=a.id";
		}
		else 
		{
			$query .= " LEFT JOIN #__viewlevels AS a ON c.access=a.id";
		}
		if (isset($filters['parent']) && $filters['parent'] != 0) 
		{
			$query .= " WHERE (c.parent=" . $this->_db->Quote($filters['parent']) . " OR c.id=" . $this->_db->Quote($filters['parent']) . ")";
			if (isset($filters['state'])) 
			{
				$query .= " AND c.state=" . $this->_db->Quote($filters['state']);
			}
			if (!isset($filters['sort']) || !$filters['sort']) 
			{
				$filters['sort'] = 'c.created';
			}
			if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
			{
				$filters['sort_Dir'] = 'ASC';
			}
			$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
		} 
		else 
		{
			$where = array();
			
			if (isset($filters['state'])) 
			{
				$where[] = "c.state=" . $this->_db->Quote($filters['state']);
			}
			if (isset($filters['sticky']) && $filters['sticky'] != 0) 
			{
				$where[] = "c.sticky=" . $this->_db->Quote($filters['sticky']);
			}
			if (isset($filters['group']) && $filters['group'] >= 0) 
			{
				$where[] = "c.group_id=" . $this->_db->Quote($filters['group']);
			}
			if (isset($filters['category_id']) && $filters['category_id'] >= 0) 
			{
				$where[] = "c.category_id=" . $this->_db->Quote($filters['category_id']);
			}
			//if (!isset($filters['authorized']) || !$filters['authorized']) {
			//	$query .= "c.access=0 AND ";
			//}
			if (isset($filters['search']) && $filters['search'] != '') 
			{
				$where[] = "(LOWER(c.title) LIKE '%" . strtolower($filters['search']) . "%' 
						OR LOWER(c.comment) LIKE '%" . strtolower($filters['search']) . "%')";
			}
			if (isset($filters['parent']) && $filters['parent'] >= 0) 
			{
				$where[] = "c.parent=" . $this->_db->Quote($filters['parent']);
			}
			
			if (count($where) > 0)
			{
				$query .= " WHERE ";
				$query .= implode(" AND ", $where);
			}
			
			if (isset($filters['limit']) && $filters['limit'] != 0) 
			{
				if (isset($filters['sticky']) && $filters['sticky'] == false) 
				{
					if (!isset($filters['sort']) || !$filters['sort']) 
					{
						$filters['sort'] = 'activity DESC, c.created';
					}
					if (!isset($filters['sort_Dir']) || !$filters['sort_Dir']) 
					{
						$filters['sort_Dir'] = 'DESC';
					}
					$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
				} 
				else 
				{
					$query .= " ORDER BY c.sticky DESC, activity DESC, c.created DESC";
				}
			}
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
		$query = "SELECT c.*, g.cn AS group_alias";
		if (!isset($filters['parent']) || $filters['parent'] == 0) 
		{
			$query .= ", (SELECT COUNT(*) FROM $this->_tbl AS r WHERE r.parent=c.id AND r.state<2) AS replies ";
			//$query .= ", (SELECT d.created FROM $this->_tbl AS d WHERE d.parent=c.id ORDER BY created DESC LIMIT 1) AS last_activity ";
			$query .= ", (CASE WHEN c.last_activity != '0000-00-00 00:00:00' THEN c.last_activity ELSE c.created END) AS activity";
		}
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= ", a.name AS access_level";
		}
		else 
		{
			$query .= ", a.title AS access_level";
		}
		$query .= $this->buildQuery($filters);

		if ($filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
	
	/**
	 * Get a list of all participants in a thread
	 * 
	 * @param      array $filters Filters to build query from
	 * @return     array
	 */
	public function getParticipants($filters=array())
	{
		$query = "SELECT DISTINCT c.anonymous, c.created_by, u.name 
					FROM $this->_tbl AS c 
					LEFT JOIN #__users AS u ON c.created_by=u.id 
					WHERE ";

		if (isset($filters['category_id'])) 
		{
			$where[] = "c.category_id = " . $this->_db->Quote($filters['category_id']);
		}
		$where[] = "(c.parent = " . $this->_db->Quote($filters['parent']) . " OR c.id = " . $this->_db->Quote($filters['parent']) . ")";

		$query .= implode(" AND ", $where);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Get the last post in a thread
	 * 
	 * @param      integer $parent Thread ID
	 * @return     object
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
		$obj = $this->_db->loadObject();
		if (is_array($obj))
		{
			return $obj[0];
		}
		return $obj;
	}
	
	/**
	 * Get the last activity for a category
	 * 
	 * @param      integer $group_id    Group ID
	 * @param      integer $category_id Category ID
	 * @return     object
	 */
	public function getLastActivity($group_id=null, $category_id=null)
	{
		$query = "SELECT r.* FROM $this->_tbl AS r";
		$where = array();
		if ($group_id !== null)
		{
			$where[] = "r.group_id=$group_id";
		}
		if ($category_id !== null)
		{
			$where[] = "r.category_id=$category_id";
		}
		if (count($where) > 0) 
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}
		$query .= " ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery($query);
		$obj = $this->_db->loadObject();
		if (is_array($obj))
		{
			return $obj[0];
		}
		return $obj;
	}

	/**
	 * Delete replies to a post
	 * 
	 * @param      integer $parent Thread ID
	 * @return     boolean True on success
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

	/**
	 * Update all replies to a post
	 * 
	 * @param      array   $data   Data to update posts with
	 * @param      integer $parent Parent ID
	 * @return     boolean True on success
	 */
	public function updateReplies($data=array(), $parent=null)
	{
		if (!$parent) 
		{
			$parent = $this->parent;
		}
		if (!$parent) 
		{
			return false;
		}

		if (empty($data))
		{
			return false;
		}

		$set = array();
		foreach ($data as $key => $val)
		{
			$set[] = $key . '=' . $this->_db->Quote($val);
		}
		$values = implode(', ', $set);

		$this->_db->setQuery("UPDATE $this->_tbl SET $values WHERE parent=$parent");
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

	/**
	 * Set a new category for all records of a previous category
	 * 
	 * @param      integer $old      Old category ID
	 * @param      integer $nw       New category ID
	 * @param      integer $group_id Group ID
	 * @return     boolean True on success
	 */
	public function updateCategory($old=null, $nw=null, $group_id=0)
	{
		if ($old === null) 
		{
			$old = $this->category_id;
		}
		if ($nw === null || $old === null) 
		{
			return false;
		}

		$this->_db->setQuery("UPDATE $this->_tbl SET category_id=$nw WHERE category_id=$old AND group_id=$group_id");
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

	/**
	 * Delete all records in a category
	 * 
	 * @param      integer $oid Record ID
	 * @return     boolean True on success
	 */
	public function deleteByCategory($oid=null)
	{
		$oid = intval($oid);
		if ($oid === null) 
		{
			return false;
		}

		$query = 'DELETE FROM ' . $this->_db->nameQuote($this->_tbl) . ' WHERE category_id = ' . $this->_db->Quote($oid);
		$this->_db->setQuery($query);
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}

		return true;
	}

	/**
	 * Delete a record and any children
	 * 
	 * @param      integer $oid Record ID
	 * @return     boolean True on success
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = intval($oid);
		}

		$this->load($this->$k);
		if (!$this->parent)
		{
			$query = 'DELETE FROM ' . $this->_db->nameQuote($this->_tbl) . ' WHERE parent = ' . $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}

		return parent::delete($oid);
	}

	/**
	 * Set the state of posts by the category
	 * 
	 * @param      integer $cat   Category ID
	 * @param      integer $state State to set (0, 1, 2)
	 * @return     boolean True on success
	 */
	public function setStateByCategory($cat=null, $state=null)
	{
		if ($cat === null) 
		{
			$cat = $this->category_id;
		}
		if ($state === null || $cat === null) 
		{
			return false;
		}
		
		if (is_array($cat))
		{
			$cat = array_map('intval', $cat);
			$cat = implode(',', $cat);
		}
		else 
		{
			$cat = intval($cat);
		}
		
		$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE category_id IN ($cat)");
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
