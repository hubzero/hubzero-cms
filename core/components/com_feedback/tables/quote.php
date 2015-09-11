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
 * @author    Alissa Nedossekina <alisa@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Feedback\Tables;

use Component;
use Lang;

/**
 * Table class for feedback quote
 */
class Quote extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
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
			$this->setError(Lang::txt('Quote must contain text.'));
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
			$where[] = "`notable_quote`=" . $this->_db->quote($filters['notable_quote']);
		}

		if (isset($filters['search']) && $filters['search'] != '')
		{
			$words = explode(' ', $filters['search']);
			$sqlsearch = array();

			foreach ($words as $word)
			{
				$sqlsearch[] = "(LOWER(`fullname`) LIKE " . $this->_db->quote('%' . strtolower($word) . '%') . ")";
			}

			$where[] = "(" . implode(" OR ", $sqlsearch) . ")";
		}

		if (isset($filters['id']) && $filters['id'] != 0)
		{
			$where[] = "`id`=" . $this->_db->quote($filters['id']);
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
				$filters['sort']     = isset($filters['sort'])     ? $filters['sort'] : '`date`';
				$filters['sort_Dir'] = isset($filters['sort_Dir']) ? $filters['sort_Dir'] : 'DESC';

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
			$this->setError(Lang::txt('JLIB_DATABASE_ERROR_NULL_PRIMARY_KEY'));
			return false;
		}

		$path =$this->filespace() . DS . $pk;
		$this->_delTree($path);

		return parent::delete($pk);
	}

	/**
	 * Recursively remove files and directories
	 *
	 * @param   string  $dir  Directory to remove
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

	/**
	 * Return the path for uploads
	 *
	 * @return  string
	 */
	public function filespace($root = true)
	{
		$config = Component::params('com_feedback');

		return ($root ? PATH_APP : '') . DS . trim($config->get('uploadpath', '/site/quotes'), DS);
	}
}

