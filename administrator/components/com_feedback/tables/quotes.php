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
 * @copyright Copyright 2005-2011 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for feedback quote
 */
class FeedbackQuotes extends JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  JDatabase
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__feedback', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		$this->quote = trim($this->quote);
		if ($this->quote == '')
		{
			$this->setError(JText::_('Quote must contain text.'));
			return false;
		}
		$this->quote = str_replace('<br>', '<br />', $this->quote);

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	protected function _buildQuery($filters)
	{
		$query = "FROM `$this->_tbl`";

		$where = array();

		if (isset($filters['notable_quote']) && $filters['notable_quote'] >= 0)
		{
			$where[] = "`notable_quote`=" . $this->_db->Quote($filters['notable_quote']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$words = explode(' ', $filters['search']);
			$sqlsearch = array();

			foreach ($words as $word)
			{
				$sqlsearch[] = "(LOWER(`fullname`) LIKE " . $this->_db->Quote('%' . strtolower($word) . '%') . ")";
			}

			$where[] = "(" . implode(" OR ", $sqlsearch) . ")";
		}

		if (isset($filters['id']) && $filters['id'] != 0)
		{
			$where[] = "`id`=" . $this->_db->Quote($filters['id']);
		}

		if (count($where))
		{
			$query .= " WHERE " . implode(" AND ", $where);
		}

		return $query;
	}

	/**
	 * Get a count of, single entry, or list of entries
	 * 
	 * @param   string   $rtrn     Data to return
	 * @param   array    $filters  Filters to apply to data retrieval
	 * @param   array    $select   List of fields to select
	 * @return  mixed
	 */
	public function find($what='', $filters=array(), $select=array('*'))
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
				$filters['sort']     = isset($filters['sort'])     ?: '`date`';
				$filters['sort_Dir'] = isset($filters['sort_Dir']) ?: 'DESC';

				if ($filters['sort_Dir'])
				{
					$filters['sort_Dir'] = strtoupper($filters['sort_Dir']);
					if (!in_array($filters['sort_Dir'], array('ASC', 'DESC')))
					{
						$filters['sort_Dir'] = 'DESC';
					}
				}

				$query  = "SELECT " . implode(', ', $select) . " " . $this->_buildQuery($filters);
				$query .= " ORDER BY " . $filters['sort'] . " " . $filters['sort_Dir'];

				if (isset($filters['limit']) && $filters['limit'] > 0)
				{
					$filters['start'] = (isset($filters['start']) ? $filters['start'] : 0);

					$query .= " LIMIT " . (int) $filters['start'] . "," . (int) $filters['limit'];
				}

				$this->_db->setQuery($query);
				return $this->_db->loadObjectList();
			break;
		}
	}

	/**
	 * Method to delete a row from the database table by primary key value.
	 *
	 * @param   mixed    $pk  An optional primary key value to delete.  If not set the instance property value is used.
	 * @return  boolean  True on success.
	 */
	public function delete($pk = null)
	{
		// Initialise variables.
		$k = $this->_tbl_key;
		$pk = (is_null($pk)) ? $this->$k : $pk;

		// If no primary key is given, return false.
		if ($pk === null)
		{
			$e = new JException(JText::_('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			$this->setError($e);
			return false;
		}

		$config = JComponentHelper::getParams('com_feedback');

		$path = JPATH_ROOT . DS . trim($config->get('uploadpath', '/site/quotes'), DS) . DS . $pk;
		$this->_delTree($path);

		return parent::delete($pk);
	}

	/**
	 * Recursively remove files and directories
	 *
	 * @param   string $dir Directory to remove
	 * @return  void
	 */
	private function _delTree($dir)
	{
		$files = array_diff(scandir($dir), array('.', '..'));

		foreach ($files as $file)
		{
			(is_dir("$dir/$file")) ? $this->_delTree("$dir/$file") : unlink("$dir/$file");
		}
		rmdir($dir);
	}
}

