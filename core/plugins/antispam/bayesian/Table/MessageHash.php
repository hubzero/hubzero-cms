<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 HUBzero Foundation, LLC.
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
 * @copyright Copyright 2009-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Antispam\Bayesian\Table;

/**
 * Message hash table
 */
class MessageHash extends \JTable
{
	/**
	 * Constructor method for JTable class
	 *
	 * @param   object  $db
	 * @return  void
	 */
	public function __construct($db)
	{
		parent::__construct('#__antispam_message_hashes', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean
	 */
	public function check()
	{
		$this->hash = trim($this->hash);
		if (!$this->hash)
		{
			$this->setError(\Lang::txt('Entry must contain a hash.'));
			return false;
		}

		return true;
	}

	/**
	 * Delete records by hash
	 *
	 * @param   string   $hash
	 * @return  boolean
	 */
	public function deleteByHash($hash)
	{
		$this->_db->setQuery("DELETE FROM `$this->_tbl` WHERE `hash`=" . $this->_db->quote($hash));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get Records
	 *
	 * @param   string  $what
	 * @param   array   $filters
	 * @return  mixed
	 */
	public function find($what='list', $filters = array())
	{
		$what = strtolower($what);

		switch ($what)
		{
			case 'count':
				$query = "SELECT COUNT(*) " . $this->_buildQuery($filters);

				$this->_db->setQuery($query);
				return $this->_db->loadResult();
			break;

			case 'one':
				$filters['limit'] = 1;

				$result = null;
				if ($results = $this->find('list', $filters))
				{
					$result = $results[0];
				}

				return $result;
			break;

			case 'first':
				$filters['start'] = 0;

				return $this->find('one', $filters);
			break;

			case 'all':
				if (isset($filters['limit']))
				{
					unset($filters['limit']);
				}
				return $this->find('list', $filters);
			break;

			case 'list':
			default:
				if (!isset($filters['sort']))
				{
					$filters['sort'] = $this->_tbl_key;
				}
				if (!isset($filters['sort_Dir']))
				{
					$filters['sort_Dir'] = 'ASC';
				}
				if ($filters['sort_Dir'])
				{
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'ASC';
					}
				}

				$query  = "SELECT * " . $this->_buildQuery($filters);
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];
				if (isset($filters['limit']))
				{
					if (!isset($filters['start']))
					{
						$filters['start'] = 0;
					}
					$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Build Query
	 *
	 * @param   array   $filters
	 * @return  string
	 */
	private function _buildQuery($filters = array())
	{
		$where = array();

		$query = "FROM `$this->_tbl`";

		if (isset($filters['hash']) && $filters['hash'])
		{
			if (is_array($filters['hash']))
			{
				foreach ($filters['hash'] as $i => $word)
				{
					$filters['hash'][$i] = $this->_db->quote($word);
				}
				$where[] = "`hash` IN (" . implode(",", $filters['hash']) . ")";
			}
			else
			{
				$where[] = "`hash` = " . $this->_db->Quote($filters['hash']);
			}
		}

		//if we have an wheres append them
		if (count($where) > 0)
		{
			$query .= " WHERE " . implode(' AND ', $where);
		}

		return $query;
	}
}
