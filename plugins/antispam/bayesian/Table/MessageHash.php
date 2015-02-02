<?php
/**
 * HUBzero CMS
 *
 * Copyright 2009-2015 Purdue University. All rights reserved.
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
 * @copyright Copyright 2009-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
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
			$this->setError(\JText::_('Entry must contain a hash.'));
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
		$select = (array) $select;

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
