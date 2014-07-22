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
 * @license   GNU General Public License, version 2 (GPLv2)
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

/**
 * Table class for event pages
 */
class EventsPage extends JTable
{
	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $id          = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $event_id    = NULL;

	/**
	 * string(100)
	 *
	 * @var string
	 */
	var $alias       = NULL;

	/**
	 * string(250)
	 *
	 * @var string
	 */
	var $title       = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $pagetext    = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $created     = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $created_by  = NULL;

	/**
	 * datetime(0000-00-00 00:00:00)
	 *
	 * @var string
	 */
	var $modified    = NULL;

	/**
	 * int(11)
	 *
	 * @var itneger
	 */
	var $modified_by = NULL;

	/**
	 * int(11)
	 *
	 * @var integer
	 */
	var $ordering    = NULL;

	/**
	 * text
	 *
	 * @var string
	 */
	var $params      = NULL;

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
			$this->setError(JText::_('COM_EVENTS_PAGE_MUST_HAVE_ALIAS'));
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
		$this->_db->setQuery("SELECT * FROM $this->_tbl WHERE alias=" . $this->_db->Quote($alias) . " AND event_id=" . intval($event_id));
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

