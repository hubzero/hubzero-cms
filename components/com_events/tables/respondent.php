<?php
/**
 * @package     hubzero-cms
 * @copyright   Copyright 2005-2011 Purdue University. All rights reserved.
 * @license     GNU General Public License, version 2 (GPLv2) 
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
 */

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

class EventsRespondent extends JTable
{
	public $id = NULL;
	public $event_id = NULL;
	public $registered = NULL;
	public $first_name = NULL;
	public $last_name = NULL;
	public $affiliation = NULL;
	public $title = NULL;
	public $city = NULL;
	public $state = NULL;
	public $zip = NULL;
	public $country = NULL;
	public $telephone = NULL;
	public $fax = NULL;
	public $email = NULL;
	public $website = NULL;
	public $position_description = NULL;
	public $highest_degree = NULL;
	public $gender = NULL;
	public $disability_needs = NULL;
	public $dietary_needs = NULL;
	public $attending_dinner = NULL;
	public $abstract = NULL;
	public $comment = NULL;
	public $arrival = NULL;
	public $departure = NULL;

	private $filters      = array();
	private $order        = NULL;
	private $order_desc   = NULL;
	private $search_terms = '';
	private $limit        = 0;
	private $offset       = 0;

	public static function getRacialIdentification($resp_id)
	{
		$dbh =& JFactory::getDBO();
		if (is_array($resp_id)) {
			$dbh->setQuery('SELECT respondent_id, group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') AS identification FROM #__events_respondent_race_rel WHERE respondent_id IN ('. implode(', ', array_map('intval', $resp_id)) . ') GROUP BY respondent_id');
			return $dbh->loadAssocList('respondent_id');
		} else {
			$dbh->setQuery('SELECT group_concat(concat(race, coalesce(concat(\'(\', tribal_affiliation, \')\'), \'\')) separator \', \') FROM #__events_respondent_race_rel WHERE respondent_id = '.intval($resp_id).' GROUP BY respondent_id');
			return $dbh->loadResult();
		}
	}

	public function getSearchTerms()
	{
		return $this->search_terms;
	}

	public function getOrdering()
	{
		return $this->order_desc;
	}

	public function getPaginator()
	{
		jimport('joomla.html.pagination');
		return new JPagination($this->getCount(), $this->limit, $this->offset);
	}

	public function __construct($filters)
	{
		parent::__construct('#__events_respondents', 'id', JFactory::getDBO());

		if (array_key_exists('sortby', $filters)) {
			if (preg_match('/(registered|name|special|id)(?:\ (ASC|DESC))?/', $filters['sortby'], $match)) {
				if ($match[1] == 'name') {
					$this->order_desc = 'name '.$match[2];
					$this->order = ' ORDER BY last_name, first_name';
				} else if ($match[1] == 'special') {
					$this->order_desc = 'special '.$match[2];
					$this->order = ' ORDER BY CASE WHEN disability_needs OR dietary_needs IS NOT NULL THEN 1 WHEN comment IS NOT NULL THEN 2 ELSE 3 END '.$match[2];
				} else {
					$this->order_desc = $filters['sortby'];
					$this->order = ' ORDER BY ' . $filters['sortby'];
				}
				unset($filters['sortby']);
			} else {
				throw new Exception('Invalid sorting criterium: '.$filters['sortby']);
			}
		}

		if (array_key_exists('limit', $filters)) {
			$this->limit = intval($filters['limit']);
			$this->offset = array_key_exists('offset', $filters) ? intval($filters['offset']) : 0;

			if (array_key_exists('offset', $filters)) unset($filters['offset']);
			unset($filters['limit']);
		}

		foreach ($filters as $key=>$val)
		{
			switch ($key)
			{
				case 'id':
					$this->filters[] = (is_array($val))
						? 'event_id IN ('.implode(', ', array_map('intval', $val)).')'
						: 'event_id = '.intval($val);
				break;
				case 'respondent_id':
					$this->filters[] = 'id = '.intval($val);
				break;
				case 'search':
					if (!empty($val)) {
						$this->filters[] = 'concat(first_name, \' \', last_name) LIKE \'%'.mysql_real_escape_string($val).'%\'';
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

	public function fetch($bounded = true)
	{
		$this->_db->setQuery(
			'SELECT '.($bounded ? '*' : 'COUNT(*)')." FROM $this->_tbl WHERE ".$this->filters.$this->order.
				($bounded && $this->limit != 0 ? ' LIMIT '.$this->offset.', '.$this->limit : '')
		);
		return $bounded ? $this->_db->loadObjectList() : $this->_db->loadResult();
	}

	public function getRecords()
	{
		return $this->fetch();
	}

	public function getCount()
	{
		return $this->fetch(false);
	}

	public function deleteRespondents( $event_id=NULL )
	{
		if ($event_id === NULL) {
			return false;
		}
		$this->_db->setQuery( "DELETE FROM $this->_tbl WHERE event_id='$event_id'" );
		return $this->_db->loadObjectList();
	}
}

