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
 * Table class for post votes
 */
class CollectionsTableVote extends JTable
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
	var $user_id  = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $item_id = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $voted      = NULL;

	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_votes', 'id', $db);
	}

	/**
	 * Load a record by its bulletin and user IDs
	 *
	 * @param      integer $item_id Bulletin ID
	 * @param      integer $user_id     User ID
	 * @return     boolean True upon success, False if errors
	 */
	public function loadByBulletin($item_id=null, $user_id=null)
	{
		$fields = array(
			'item_id' => (int) $item_id,
			'user_id' => (int) $user_id
		);

		return parent::load($fields);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(JText::_('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
			return false;
		}

		if (!$this->id)
		{
			$this->voted   = JFactory::getDate()->toSql();
			$this->user_id = JFactory::getUser()->get('id');
		}

		return true;
	}

	/**
	 * Get a total of all likes
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getLikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->Quote(intval($filters['user_id']));
		}
		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "s.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "s.collection_id=" . $this->_db->Quote(intval($filters['collection_id']));
			}
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get a total of all dislikes
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     integer
	 */
	public function getDislikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->Quote(intval($filters['user_id']));
		}
		if (isset($filters['collection_id']) && $filters['collection_id'])
		{
			if (is_array($filters['collection_id']))
			{
				$filters['collection_id'] = array_map('intval', $filters['collection_id']);
				$where[] = "s.collection_id IN (" . implode(',', $filters['collection_id']) . ")";
			}
			else
			{
				$where[] = "s.collection_id=" . $this->_db->Quote(intval($filters['collection_id']));
			}
		}
		if (count($where) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $where);
		}

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}
}
