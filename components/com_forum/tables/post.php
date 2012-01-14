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

//----------------------------------------------------------
// XForum database class
//----------------------------------------------------------

/**
 * Short description for 'XForum'
 * 
 * Long description (if any) ...
 */
class ForumPost extends JTable
{
	/**
	 * Description for 'id'
	 * 
	 * @var integer int(11) Primary key
	 */
	var $id         = NULL;

	/**
	 * Description for 'category_id'
	 * 
	 * @var integer int(11)
	 */
	var $category_id = NULL;
	
	/**
	 * Description for 'title'
	 * 
	 * @var string  varchar(255)
	 */
	var $title      = NULL;

	/**
	 * Description for 'comment'
	 * 
	 * @var string  text
	 */
	var $comment    = NULL;

	/**
	 * Description for 'created'
	 * 
	 * @var string  datetime(0000-00-00 00:00:00)
	 */
	var $created    = NULL;

	/**
	 * Description for 'created_by'
	 * 
	 * @var integer int(11)
	 */
	var $created_by = NULL;

	/**
	 * Description for 'modified'
	 * 
	 * @var string  datetime(0000-00-00 00:00:00)
	 */
	var $modified   = NULL;

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
	 * Description for 'sticky'
	 * 
	 * @var unknown
	 */
	var $sticky     = NULL;  // @var int(2)

	/**
	 * Description for 'parent'
	 * 
	 * @var unknown
	 */
	var $parent     = NULL;  // @var int(11)

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
	 * Description for 'anonymous'
	 * 
	 * @var unknown
	 */
	var $anonymous  = NULL;  // @var tinyint(2)
	
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
	 * Short description for '__construct'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown &$db Parameter description (if any) ...
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__forum_posts', 'id', $db);
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
	 * Short description for 'buildQuery'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     string Return description (if any) ...
	 */
	public function buildQuery($filters=array())
	{
		$query  = " FROM $this->_tbl AS c";
		$query .= " LEFT JOIN #__xgroups AS g ON g.gidNumber=c.group_id";
		if (isset($filters['parent']) && $filters['parent'] != 0) 
		{
			$query .= " WHERE c.parent=" . $this->_db->Quote($filters['parent']) . " OR c.id=" . $this->_db->Quote($filters['parent']);
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

		$query = "SELECT COUNT(*) " . $this->buildQuery($filters);

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
		$query = "SELECT c.*, g.cn AS group_alias";
		if (!isset($filters['parent']) || $filters['parent'] == 0) 
		{
			$query .= ", (SELECT COUNT(*) FROM $this->_tbl AS r WHERE r.parent=c.id AND r.state<2) AS replies ";
			//$query .= ", (SELECT d.created FROM $this->_tbl AS d WHERE d.parent=c.id ORDER BY created DESC LIMIT 1) AS last_activity ";
			$query .= ", (CASE WHEN c.last_activity != '0000-00-00 00:00:00' THEN c.last_activity ELSE c.created END) AS activity";
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
	 * Short description for 'getRecords'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      array $filters Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getParticipants($filters=array())
	{
		$query = "SELECT DISTINCT c.anonymous, c.created_by, u.name 
					FROM $this->_tbl AS c 
					LEFT JOIN #__users AS u ON c.created_by=u.id 
					WHERE ";

		/*if (isset($filters['group']) && $filters['group'] != 0) 
		{
			$where[] = "c.group_id = " . $this->_db->Quote($filters['group']);
		}*/
		if (isset($filters['category_id'])) 
		{
			$where[] = "c.category_id = " . $this->_db->Quote($filters['category_id']);
		}
		$where[] = "(c.parent = " . $this->_db->Quote($filters['parent']) . " OR c.id = " . $this->_db->Quote($filters['parent']) . ")";
		//$where[] = "c.state = " . $this->_db->Quote(1);
		//$where[] = "c.anonymous != " . $this->_db->Quote(1);
		
		$query .= implode(" AND ", $where);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Short description for 'getLastPost'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parent Parameter description (if any) ...
	 * @return     object Return description (if any) ...
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
	 * Short description for 'getLastPost'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parent Parameter description (if any) ...
	 * @return     object Return description (if any) ...
	 */
	public function getLastActivity($group_id=null)
	{
		$query = "SELECT r.* FROM $this->_tbl AS r ";
		if ($group_id)
		{
			$query .= "WHERE r.group_id=$group_id ";
		}
		$query .= "ORDER BY created DESC LIMIT 1";

		$this->_db->setQuery($query);
		$obj = $this->_db->loadObject();
		if (is_array($obj))
		{
			return $obj[0];
		}
		return $obj;
	}

	/**
	 * Short description for 'deleteReplies'
	 * 
	 * Long description (if any) ...
	 * 
	 * @param      unknown $parent Parameter description (if any) ...
	 * @return     boolean Return description (if any) ...
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
	
	public function delete($oid=null)
	{
		$k = $this->_tbl_key;
		if ($oid) {
			$this->$k = intval( $oid );
		}
		
		$this->load($this->$k);
		if (!$this->parent)
		{
			$query = 'DELETE FROM '.$this->_db->nameQuote($this->_tbl) .' WHERE parent = '. $this->_db->Quote($this->$k);
			$this->_db->setQuery($query);
			if (!$this->_db->query())
			{
				$this->setError($this->_db->getErrorMsg());
				return false;
			}
		}
		
		return parent::delete($oid);
	}
}
