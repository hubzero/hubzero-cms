<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Exception;

/**
 * Event respondent
 */
class Respondent extends Table
{
	/**
	 * Description for 'filters'
	 *
	 * @var  mixed
	 */
	private $filters = array();

	/**
	 * Ordering column
	 *
	 * @var string
	 */
	private $order = null;

	/**
	 * Ordering direction
	 *
	 * @var  string
	 */
	private $order_desc = null;

	/**
	 * Search terms
	 *
	 * @var  string
	 */
	private $search_terms = '';

	/**
	 * Record limit
	 *
	 * @var  integer
	 */
	private $limit = 0;

	/**
	 * Record offset
	 *
	 * @var  integer
	 */
	private $offset = 0;

	/**
	 * Get racial information for a user
	 *
	 * @param    integer  $resp_id
	 * @return   mixed
	 */
	public static function getRacialIdentification($resp_id)
	{
		$dbh = \App::get('db');
		if (is_array($resp_id))
		{
			$dbh->setQuery('SELECT respondent_id, group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') AS identification FROM #__events_respondent_race_rel WHERE respondent_id IN (' . implode(', ', array_map('intval', $resp_id)) . ') GROUP BY respondent_id');
			return $dbh->loadAssocList('respondent_id');
		}
		else
		{
			$dbh->setQuery('SELECT group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') FROM #__events_respondent_race_rel WHERE respondent_id = ' . intval($resp_id) . ' GROUP BY respondent_id');
			return $dbh->loadResult();
		}
	}

	/**
	 * Get search terms
	 *
	 * @return  string
	 */
	public function getSearchTerms()
	{
		return $this->search_terms;
	}

	/**
	 * Get ordering
	 *
	 * @return  string
	 */
	public function getOrdering()
	{
		return $this->order_desc;
	}

	/**
	 * Create a pagination object
	 *
	 * @return  object
	 */
	public function getPaginator()
	{
		return new \Hubzero\Pagination\Paginator($this->getCount(), $this->offset, $this->limit);
	}

	/**
	 * Constructor
	 *
	 * @param   array  $filters
	 * @return  void
	 * @throws  Exception
	 */
	public function __construct($filters)
	{
		parent::__construct('#__events_respondents', 'id', \App::get('db'));

		if (array_key_exists('sortby', $filters))
		{
			if (preg_match('/(registered|name|special|id)(?:\ (ASC|DESC))?/', $filters['sortby'], $match))
			{
				if ($match[1] == 'name')
				{
					$this->order_desc = 'name ' . $match[2];
					$this->order = ' ORDER BY last_name, first_name';
				}
				else if ($match[1] == 'special')
				{
					$this->order_desc = 'special ' . $match[2];
					$this->order = ' ORDER BY CASE WHEN disability_needs OR dietary_needs IS NOT null THEN 1 WHEN comment IS NOT null THEN 2 ELSE 3 END ' . $match[2];
				}
				else
				{
					$this->order_desc = $filters['sortby'];
					$this->order = ' ORDER BY ' . $filters['sortby'];
				}
				unset($filters['sortby']);
			}
			else
			{
				throw new Exception('Invalid sorting criterium: ' . $filters['sortby']);
			}
		}

		if (array_key_exists('limit', $filters))
		{
			$this->limit  = intval($filters['limit']);
			$this->offset = array_key_exists('offset', $filters) ? intval($filters['offset']) : 0;

			if (array_key_exists('offset', $filters))
			{
				unset($filters['offset']);
			}
			unset($filters['limit']);
		}

		foreach ($filters as $key => $val)
		{
			switch ($key)
			{
				case 'id':
					$this->filters[] = (is_array($val))
						? 'event_id IN (' . implode(', ', array_map('intval', $val)) . ')'
						: 'event_id = ' . intval($val);
				break;
				case 'respondent_id':
					$this->filters[] = 'id = ' . intval($val);
				break;
				case 'search':
					if (!empty($val))
					{
						$this->filters[] = "concat(first_name, ' ', last_name) LIKE " . $this->_db->quote($val . '%');
						$this->searchTerms = htmlentities($val);
					}
				break;
				default:
					throw new Exception('Unhandled filter type ' . $key);
				break;
			}
		}
		$this->filters = implode(' AND ', array_values($this->filters));
	}

	/**
	 * Get records or a count of records
	 *
	 * @param   boolean  $bounded
	 * @return  mixed
	 */
	public function fetch($bounded = true)
	{
		$this->_db->setQuery(
			'SELECT ' . ($bounded ? '*' : 'COUNT(*)') . " FROM $this->_tbl WHERE " . $this->filters . $this->order .
				($bounded && $this->limit != 0 ? ' LIMIT ' . $this->offset . ', ' . $this->limit : '')
		);
		return $bounded ? $this->_db->loadObjectList() : $this->_db->loadResult();
	}

	/**
	 * Get a list of records
	 *
	 * @return  array
	 */
	public function getRecords()
	{
		return $this->fetch();
	}

	/**
	 * Get a count
	 *
	 * @return  integer
	 */
	public function getCount()
	{
		return $this->fetch(false);
	}

	/**
	 * Delete respondents for an event
	 *
	 * @param   integer  $event_id
	 * @return  boolean
	 */
	public function deleteRespondents($event_id=null)
	{
		if ($event_id === null)
		{
			return false;
		}
		$this->_db->setQuery("DELETE FROM $this->_tbl WHERE event_id=" . $this->_db->quote(intval($event_id)));
		return $this->_db->query();
	}

	/**
	 * Check for unique registration per event
	 *
	 * @param   string  $email
	 * @param   int     $eventId
	 * @return  int
	 */
	public static function checkUniqueEmailForEvent($email, $eventId)
	{
		$db = \App::get('db');
		$sql = "SELECT COUNT(*) FROM `#__events_respondents` WHERE `event_id`=" . $db->quote($eventId) . " AND `email`=" .$db->quote($email);
		$db->setQuery($sql);
		return $db->loadResult();
	}
}
