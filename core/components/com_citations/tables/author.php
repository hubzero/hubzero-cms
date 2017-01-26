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

namespace Components\Citations\Tables;

/**
 * Table class for citation authors
 */
class Author extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__citations_authors', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		if (trim($this->cid) == '')
		{
			$this->setError(Lang::txt('AUTHOR_MUST_HAVE_CITATION_ID'));
		}
		if (trim($this->author) == '')
		{
			$this->setError(Lang::txt('AUTHOR_MUST_HAVE_TEXT'));
		}

		if ($this->getError())
		{
			return false;
		}

		if (!$this->id)
		{
			$sql = "SELECT ordering from $this->_tbl WHERE `cid`=" . $this->_db->quote(intval($this->cid)) . " ORDER BY ordering DESC LIMIT 1";
			$this->_db->setQuery($sql);
			$this->ordering = intval($this->_db->loadResult());
			$this->ordering++;
		}

		return true;
	}

	/**
	 * Build a query from filters
	 *
	 * @param   array   $filters  Filters to build query from
	 * @return  string  SQL
	 */
	public function buildQuery($filters)
	{
		$query = "";
		$ands = array();
		if (isset($filters['cid']) && $filters['cid'] != 0)
		{
			$ands[] = "r.cid=" . $this->_db->quote($filters['cid']);
		}
		if (isset($filters['author_uid']) && $filters['author_uid'] != 0)
		{
			$ands[] = "r.author_uid=" . $this->_db->quote($filters['author_uid']);
		}
		if (isset($filters['author']) && trim($filters['author']) != '')
		{
			$ands[] = "LOWER(r.author)=" . $this->_db->quote(strtolower($filters['author']));
		}
		if (count($ands) > 0)
		{
			$query .= " WHERE ";
			$query .= implode(" AND ", $ands);
		}
		if (isset($filters['sort']) && $filters['sort'] != '')
		{
			$query .= " ORDER BY " . $filters['sort'];
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
		}

		return $query;
	}

	/**
	 * Get a record count
	 *
	 * @param   array    $filters  Filters to build query from
	 * @return  integer
	 */
	public function getCount($filters=array())
	{
		$query  = "SELECT COUNT(*) FROM $this->_tbl AS r" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadResult();
	}

	/**
	 * Get records
	 *
	 * @param   array  $filters  Filters to build query from
	 * @return  array
	 */
	public function getRecords($filters=array())
	{
		if (!isset($filters['sort']) || $filters['sort'] == '')
		{
			$filters['sort'] = 'ordering ASC';
		}

		$query  = "SELECT * FROM $this->_tbl AS r" . $this->buildQuery($filters);

		$this->_db->setQuery($query);
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete entries for a specific citation
	 *
	 * @param   integer  $cid  Citation ID
	 * @return  boolean  True on success, False on error
	 */
	public function deleteForCitation($cid=null)
	{
		if ($cid === null)
		{
			$cid = $this->cid;
		}

		if (!$cid)
		{
			$this->setError(Lang::txt('Missing argument'));
			return false;
		}

		// Remove any types in the remove list
		$this->_db->setQuery("DELETE FROM `$this->_tbl` WHERE `cid`=" . $this->_db->quote(intval($cid)));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}
}
