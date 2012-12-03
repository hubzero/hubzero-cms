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
class BulletinboardBulletin extends JTable
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
	var $title      = NULL;

	/**
	 * text
	 * 
	 * @var string  
	 */
	var $description = NULL;

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
	var $modified    = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer 
	 */
	var $modified_by = NULL;

	/**
	 * varchar(255)
	 * 
	 * @var string  
	 */
	var $url   = NULL;

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
	var $positive   = NULL;

	/**
	 * int(11)
	 * 
	 * @var integer
	 */
	var $negative   = NULL;

	/**
	 * varchar(150)
	 * 
	 * @var string  
	 */
	var $type   = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__bulletinboard_bulletins', 'id', $db);
	}

	/**
	 * Validate data
	 * 
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->title = trim($this->title);
		$this->description = trim($this->description);
		$this->url = trim($this->url);

		if ($this->type != 'image' && $this->type != 'file' 
		 && (!$this->title && !$this->description && !$this->url)) 
		{
			$this->setError(JText::_('Please provide some content'));
			return false;
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
		$query  = " FROM $this->_tbl AS b";
		$query .= " INNER JOIN #__bulletinboard_sticks AS s ON s.bulletin_id=b.id";
		$query .= " LEFT JOIN #__users AS u ON s.created_by=u.id";

		if (isset($filters['user_id']) && $filters['user_id']) 
		{
			$query .= " LEFT JOIN #__bulletinboard_votes AS v ON v.bulletin_id=b.id AND v.user_id=" . $this->_db->Quote($filters['user_id']);
		}

		$where = array();

		if (isset($filters['board_id'])) 
		{
			$where[] = "s.board_id=" . $this->_db->Quote($filters['board_id']);
		}
		if (isset($filters['state'])) 
		{
			if (is_array($filters['state']))
			{
				$filters['state'] = array_map('intval', $filters['state']);
				$where[] = "b.state IN (" . implode(',', $filters['state']) . ")";
			}
			else
			{
				$where[] = "b.state=" . $this->_db->Quote(intval($filters['state']));
			}
		}
		/*if (isset($filters['access'])) 
		{
			if (is_array($filters['access']))
			{
				$filters['access'] = array_map('intval', $filters['access']);
				$where[] = "b.access IN (" . implode(',', $filters['access']) . ")";
			}
			else
			{
				$where[] = "b.access=" . $this->_db->Quote(intval($filters['access']));
			}
		}*/

		if (isset($filters['search']) && $filters['search'] != '') 
		{
			$where[] = "(LOWER(b.title) LIKE '%" . strtolower($filters['search']) . "%' 
					OR LOWER(b.description) LIKE '%" . strtolower($filters['search']) . "%')";
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
				$filters['sort'] = 'posted';
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

	public function getReposts($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		$id = intval($id);
		if (!$id)
		{
			return false;
		}

		$query = "SELECT COUNT(*) FROM #__bulletinboard_sticks AS s WHERE s.bulletin_id=$id AND s.original=0";
		
		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	public function getVote($id=null)
	{
		if (!$id)
		{
			$id = $this->id;
		}
		$id = intval($id);
		if (!$id)
		{
			return false;
		}

		$juser = JFactory::getUser();

		$query = "SELECT v.id FROM #__bulletinboard_votes AS v WHERE v.bulletin_id=$id AND v.user_id=" . $juser->get('id');

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
		$query = "SELECT b.*, u.name, s.created AS posted, s.created_by AS poster, s.original, 
				(SELECT COUNT(*) FROM #__bulletinboard_sticks AS s WHERE s.bulletin_id=b.id AND s.original=0) AS reposts,
				(SELECT COUNT(*) FROM #__item_comments AS c WHERE c.item_id=b.id AND c.item_type='bulletin' AND c.state IN (1, 3)) AS comments";
		if (isset($filters['user_id']) && $filters['user_id']) 
		{
			$query .= ", v.id AS voted ";
		}
		$query .= $this->buildQuery($filters);

		if ($filters['limit'] != 0) 
		{
			$query .= ' LIMIT ' . intval($filters['start']) . ',' . intval($filters['limit']);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}
}
