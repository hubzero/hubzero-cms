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
class BulletinboardBoard extends JTable
{
	/**
	 * int(11) Primary key
	 * 
	 * @var integer 
	 */
	var $id         = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string  
	 */
	var $title     = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $object_id  = NULL;
	
	/**
	 * varchar(255)
	 * 
	 * @var string  
	 */
	var $object_type = NULL;

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
	 * int(3)
	 * 
	 * @var integer 
	 */
	var $state  = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer 
	 */
	var $access = NULL;

	/**
	 * int(3)
	 * 
	 * @var integer 
	 */
	var $is_default = NULL;

	/**
	 * text
	 * 
	 * @var string
	 */
	var $description = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__bulletinboard_boards', 'id', $db);
	}

	/**
	 * Load a record by its alias and bind data to $this
	 * 
	 * @param      string $oid Record alias
	 * @return     boolean True upon success, False if errors
	 */
	public function loadDefault($object_id=null, $object_type=null)
	{
		if (!$object_id || !$object_type) 
		{
			return false;
		}
		$object_id = intval($object_id);
		$object_type = trim($object_type);

		$query = "SELECT * FROM $this->_tbl WHERE object_id='$object_id' AND object_type='$object_type' AND is_default='1' LIMIT 1";

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
	public function setup($object_id=0, $object_type='')
	{
		$result = array(
			'id'          => 0,
			'title'       => JText::_('Bulletin Board'),
			'object_id'   => $object_id,
			'object_type' => $object_type,
			'is_default'  => 1
		);
		if (!$this->bind($result))
		{
			return false;
		}
		if (!$this->check())
		{
			return false;
		}
		if (!$this->store())
		{
			return false;
		}
		return $this->loadDefault($object_id, $object_type);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);

		if (!$this->title) 
		{
			$this->setError(JText::_('Please provide a title'));
			return false;
		}

		$this->object_id = intval($this->object_id);
		if (!$this->object_id) 
		{
			$this->setError(JText::_('Please provide an object ID'));
			return false;
		}

		$this->object_type = trim($this->object_type);
		if (!$this->object_type) 
		{
			$this->setError(JText::_('Please provide an object type'));
			return false;
		}

		$juser =& JFactory::getUser();
		if (!$this->id) 
		{
			$this->created = date('Y-m-d H:i:s', time());  // use gmdate() ?
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
		$query  = " FROM $this->_tbl AS b";
		if (isset($filters['object_type']) && $filters['object_type'] == 'group') 
		{
			$query .= " LEFT JOIN #__xgroups AS g ON g.gidNumber=b.object_id";
		}

		$where = array();

		if (isset($filters['state'])) 
		{
			$where[] = "b.state=" . $this->_db->Quote(intval($filters['state']));
		}
		if (isset($filters['access'])) 
		{
			$where[] = "b.access=" . $this->_db->Quote(intval($filters['access']));
		}
		if (isset($filters['object_id']) && $filters['object_id']) 
		{
			$where[] = "b.object_id=" . $this->_db->Quote($filters['object_id']);
		}
		if (isset($filters['object_type']) && $filters['object_type']) 
		{
			$where[] = "b.object_type=" . $this->_db->Quote($filters['object_type']);
		}
		if (isset($filters['created']) && $filters['created']) 
		{
			$where[] = "b.created=" . $this->_db->Quote($filters['created']);
		}
		if (isset($filters['created_by']) && $filters['created_by']) 
		{
			$where[] = "b.created_by=" . $this->_db->Quote($filters['created_by']);
		}

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(b.title) LIKE '%" . strtolower($filters['search']) . "%')";
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
				$filters['sort'] = 'created';
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
		$query = "SELECT b.*, (SELECT COUNT(*) FROM #__bulletinboard_sticks AS s WHERE s.board_id=b.id) AS posts";
		if (isset($filters['object_type']) && $filters['object_type'] == 'group') 
		{
			$query .= ", g.cn AS group_alias";
		}
		$query .= $this->buildQuery($filters);

		if (isset($filters['limit']) && $filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
