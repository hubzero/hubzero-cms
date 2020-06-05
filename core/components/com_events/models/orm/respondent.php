<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Orm;

use Hubzero\Database\Relational;

include_once __DIR__ . '/race.php';

/**
 * Hubs database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Respondent extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'events';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'event_id' => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias',
		'modified',
		'modified_by'
	);

	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Defines a belongs to one relationship between page and event
	 *
	 * @return  object
	 */
	public function event()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\Event', 'event_id');
	}

	/**
	 * Short description for 'getRacialIdentification'
	 *
	 * Long description (if any) ...
	 *
	 * @param      unknown $resp_id Parameter description (if any) ...
	 * @return     mixed Return description (if any) ...
	 */
	public function racial()
	{
		/*
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
		}*/
		return $this->oneToMany(__NAMESPACE__ . '\Race', 'respondent_id');
	}
}
