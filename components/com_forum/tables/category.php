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
class ForumCategory extends JTable
{
	/**
	 * Description for 'id'
	 * 
	 * @var unknown
	 */
	var $id         = NULL;  // @var int(11) Primary key

	/**
	 * Description for 'title'
	 * 
	 * @var unknown
	 */
	var $title      = NULL;  // @var varchar(255)
	
	/**
	 * Description for 'alias'
	 * 
	 * @var unknown
	 */
	var $alias      = NULL;  // @var varchar(255)

	/**
	 * Description for 'comment'
	 * 
	 * @var unknown
	 */
	var $description    = NULL;  // @var text

	/**
	 * Description for 'created'
	 * 
	 * @var unknown
	 */
	var $created    = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'created_by'
	 * 
	 * @var unknown
	 */
	var $created_by = NULL;  // @var int(11)

	/**
	 * Description for 'modified'
	 * 
	 * @var unknown
	 */
	var $modified   = NULL;  // @var datetime (0000-00-00 00:00:00)

	/**
	 * Description for 'modified_by'
	 * 
	 * @var unknown
	 */
	var $modified_by = NULL;  // @var int(11)

	/**
	 * Description for 'state'
	 * 
	 * @var unknown
	 */
	var $state      = NULL;  // @var int(2)

	/**
	 * Description for 'hits'
	 * 
	 * @var unknown
	 */
	var $hits       = NULL;  // @var int(11)

	/**
	 * Description for 'group'
	 * 
	 * @var unknown
	 */
	var $group_id = NULL;  // @var int(11)

	/**
	 * Description for 'access'
	 * 
	 * @var unknown
	 */
	var $access     = NULL;  // @var tinyint(2)  0=public, 1=registered, 2=special, 3=protected, 4=private
	
	/**
	 * Description for 'section_id'
	 * 
	 * @var unknown
	 */
	var $section_id = NULL;  // @var int(11)
	
	/**
	 * Description for 'section_id'
	 * 
	 * @var unknown
	 */
	var $closed = null;
	
	/**
	 * ID for ACL asset (J1.6+)
	 * 
	 * @var int(11)
	 */
	var $asset_id = NULL;

	/**
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__forum_categories', 'id', $db);
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
		return 'com_forum.category.'.(int) $this->$k;
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

		if ($assetId === null) {
			// Build the query to get the asset id for the parent category.
			$query	= $db->getQuery(true);
			$query->select('id');
			$query->from('#__assets');
			$query->where('name = '.$db->quote('com_forum'));

			// Get the asset id from the database.
			$db->setQuery($query);
			if ($result = $db->loadResult()) {
				$assetId = (int) $result;
			}
		}

		// Return the asset id.
		if ($assetId) {
			return $assetId;
		} else {
			return parent::_getAssetParentId($table, $id);
		}
	}
	
	/**
	 * Short description for 'loadAlias'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $oid Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
	 */
	public function loadByAlias($oid=NULL, $section_id=null, $group_id=null)
	{
		if ($oid === NULL) 
		{
			return false;
		}
		$oid = trim($oid);
		
		$query = "SELECT * FROM $this->_tbl WHERE alias='$oid'";
		if ($section_id !== null)
		{
			$query .= " AND section_id=" . $section_id;
		}
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
			'title' => JText::_('Discussions'),
			'description' => JText::_('Default category for all discussions in this forum.'),
			'section_id' => 0,
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
	 * Short description for 'check'
	 * 
	 * Long description (if any) ...
	 * 
	 * @return     boolean Return description (if any) ...
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
		
		$juser =& JFactory::getUser();
		if (!$this->id)
		{
			$this->created = date('Y-m-d H:i:s', time());
			$this->created_by = $juser->get('id');
		}
		else 
		{
			$this->modified = date('Y-m-d H:i:s', time());
			$this->modified_by = $juser->get('id');
		}
		
		return true;
	}

	/**
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	protected function _buildQuery($filters=array())
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
		if (isset($filters['closed'])) 
		{
			$where[] = "c.closed=" . $this->_db->Quote($filters['closed']);
		}
		if (isset($filters['group']) && $filters['group'] >= 0) 
		{
			$where[] = "c.group_id=" . $this->_db->Quote($filters['group']);
		}
		if (isset($filters['section_id']) && $filters['section_id'] >= 0) 
		{
			$where[] = "c.section_id=" . $this->_db->Quote($filters['section_id']);
		}
		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(c.title) LIKE '%" . strtolower($filters['search']) . "%' 
				OR LOWER(c.description) LIKE '%" . strtolower($filters['search']) . "%')";
		}
		
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Short description for 'getCount'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getCount($filters=array())
	{
		$filters['limit'] = 0;

		$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getRecords($filters=array())
	{
		if (isset($filters['admin']))
		{
			$query  = "SELECT c.*, g.cn AS group_alias, 
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.parent=0) AS threads,
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id) AS posts";
		}
		else 
		{
			$query  = "SELECT c.*, g.cn AS group_alias, 
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.parent=0 AND r.state=1) AS threads,
						(SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=c.id AND r.state=1) AS posts";
		}
		if (version_compare(JVERSION, '1.6', 'lt'))
		{
			$query .= ", a.name AS access_level";
		}
		else 
		{
			$query .= ", a.title AS access_level";
		}
		$query .= " " . $this->_buildQuery($filters);

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
	
	public function getThreadCount($oid=null, $group_id=0)
	{
		$k = $this->_tbl_key;
		if ($oid !== null) 
		{
			$this->$k = intval($oid);
		}
		
		$query = "SELECT COUNT(*) FROM #__forum_posts WHERE category_id=" . $this->$k . " AND group_id=$group_id AND parent=0 AND state < 2";

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
	
	public function getPostCount($oid=null, $group_id=0)
	{
		$k = $this->_tbl_key;
		if ($oid !== null) 
		{
			$this->$k = intval($oid);
		}
		
		//$query = "SELECT COUNT(*) FROM #__forum_posts WHERE parent IN (SELECT r.id FROM #__forum_posts AS r WHERE r.category_id=" . $this->$k . " AND group_id=$group_id AND parent=0 AND state < 2)";
		$query = "SELECT COUNT(*) FROM #__forum_posts AS r WHERE r.category_id=" . $this->$k . " AND group_id=$group_id AND parent=0 AND state < 2";
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Delete a category and all associated content
	 * 
	 * @param      integer $oid Object ID (primary key)
	 * @return     true if successful otherwise returns and error message
	 */
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) 
		{
			$this->$k = intval($oid);
		}
		
		$post = new ForumPost($this->_db);
		if (!$post->deleteByCategory($this->$k))
		{
			$this->setError($post->getErrorMsg());
			return false;
		}
		
		return parent::delete();
	}
	
	public function setStateBySection($section=null, $state=null)
	{
		if ($section=== null) 
		{
			$section = $this->section_id;
		}
		if ($state === null || $section === null) 
		{
			return false;
		}
		
		if (is_array($section))
		{
			$section = array_map('intval', $section);
			$section = implode(',', $section);
		}
		else 
		{
			$section = intval($section);
		}
		
		$this->_db->setQuery("UPDATE $this->_tbl SET state=$state WHERE section_id IN ($section)");
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
