<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Shawn Rice <zooley@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Collections\Tables;

use Date;
use User;
use Lang;

/**
 * Table class for post votes
 */
class Vote extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__collections_votes', 'id', $db);
	}

	/**
	 * Load a record by its bulletin and user IDs
	 *
	 * @param   integer  $item_id  Bulletin ID
	 * @param   integer  $user_id  User ID
	 * @return  boolean  True upon success, False if errors
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
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->item_id = intval($this->item_id);
		if (!$this->item_id)
		{
			$this->setError(Lang::txt('COM_COLLECTIONS_ERROR_MISSING_ITEM_ID'));
			return false;
		}

		if (!$this->id)
		{
			$this->voted   = Date::toSql();
			$this->user_id = User::get('id');
		}

		return true;
	}

	/**
	 * Get a total of all likes
	 *
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getLikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->quote(intval($filters['user_id']));
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
				$where[] = "s.collection_id=" . $this->_db->quote(intval($filters['collection_id']));
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
	 * @param   array    $filters  Filters to construct query from
	 * @return  integer
	 */
	public function getDislikes($filters=array())
	{
		$query = "SELECT COUNT(*) FROM $this->_tbl AS v
				INNER JOIN `#__collections_posts` AS s ON s.item_id=v.item_id";

		$where = array();

		if (isset($filters['user_id']) && $filters['user_id'])
		{
			$where[] = "v.user_id=" . $this->_db->quote(intval($filters['user_id']));
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
				$where[] = "s.collection_id=" . $this->_db->quote(intval($filters['collection_id']));
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
