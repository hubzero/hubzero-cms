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
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

/**
 * Table class for event pages
 */
class Page extends \JTable
{
	/**
	 * Constructor
	 *
	 * @param      object &$db JDatabase
	 * @return     void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_pages', 'id', $db);
	}

	/**
	 * Validate data
	 *
	 * @return     boolean True if data is valid
	 */
	public function check()
	{
		$this->alias = trim($this->alias);
		if ($this->alias == '')
		{
			$this->setError(Lang::txt('COM_EVENTS_PAGE_MUST_HAVE_ALIAS'));
			return false;
		}
		return true;
	}

	/**
	 * Load the first page by alias and bind to $this
	 *
	 * @param      string  $alias    Page alias
	 * @param      integer $event_id Event ID
	 * @return     boolean True on success, false if errors
	 */
	public function loadFromAlias($alias=NULL, $event_id=NULL)
	{
		if ($alias === NULL)
		{
			return false;
		}
		if ($event_id === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->quote($alias) . " AND event_id=" . intval($event_id));
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
	 * Load the first page associated with an event and bind to $this
	 *
	 * @param      integer $event_id Event ID
	 * @return     boolean True on success, false if errors
	 */
	public function loadFromEvent($event_id=NULL)
	{
		if ($event_id === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE event_id=" . intval($event_id) . " ORDER BY ordering ASC LIMIT 1");
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
	 * Get all pages for an event
	 *
	 * @param      integer $event_id Event ID
	 * @return     array
	 */
	public function loadPages($event_id=NULL)
	{
		if ($event_id === NULL)
		{
			return false;
		}
		$this->_db->setQuery("SELECT title, alias, id FROM $this->_tbl WHERE event_id=" . intval($event_id) . " ORDER BY ordering ASC");
		return $this->_db->loadObjectList();
	}

	/**
	 * Delete all pages for an event
	 *
	 * @param      integer $event_id Event ID
	 * @return     boolean True on success, false if errors
	 */
	public function deletePages($event_id=NULL)
	{
		if ($event_id === NULL)
		{
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE event_id=" . intval($event_id));
		if (!$this->_db->query())
		{
			$this->setError($this->_db->getErrorMsg());
			return false;
		}
		return true;
	}

	/**
	 * Get the next entry in the ordering
	 *
	 * @param      string $move Direction to look for neighbor
	 * @return     boolean True on success, false if errors
	 */
	public function getNeighbor($move)
	{
		switch ($move)
		{
			case 'orderup':
			case 'orderuppage':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=" . intval($this->event_id) . " AND ordering < " . intval($this->ordering) . " ORDER BY ordering DESC LIMIT 1";
			break;

			case 'orderdown':
			case 'orderdownpage':
				$sql = "SELECT * FROM $this->_tbl WHERE event_id=" . intval($this->event_id) . " AND ordering > " . intval($this->ordering) . " ORDER BY ordering LIMIT 1";
			break;
		}
		$this->_db->setQuery($sql);
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
	 * Build a query based off of filters passed
	 *
	 * @param      array $filters Filters to construct query from
	 * @return     string SQL
	 */
	public function buildQuery($filters)
	{
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query = "SELECT t.*, NULL as position";
		}
		else
		{
			$query = "SELECT count(*)";
		}
		$query .= " FROM $this->_tbl AS t";
		if (isset($filters['event_id']) && $filters['event_id'] != '')
		{
			$query .= " WHERE t.event_id='" . intval($filters['event_id']) . "'";
		}
		if (isset($filters['search']) && $filters['search'] != '')
		{
			if (isset($filters['event_id']) && $filters['event_id'] != '')
			{
				$query .= " AND ";
			}
			else
			{
				$query .= " WHERE ";
			}
			$query .= "LOWER(t.title) LIKE " . $this->_db->quote('%' . $filters['search'] . '%');
		}
		if (isset($filters['limit']) && $filters['limit'] != 0)
		{
			$query .= " ORDER BY t.ordering ASC LIMIT " . intval($filters['start']) . "," . intval($filters['limit']);
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

		$this->_db->setQuery($this->buildQuery($filters));
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
		$this->_db->setQuery($this->buildQuery($filters));
		return $this->_db->loadObjectList();
	}
}

