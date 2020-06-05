<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Respondent race model
 *
 * @uses \Hubzero\Database\Relational
 */
class Race extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'events';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__events_respondent_race_rel';

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
		'respondent_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between page and event
	 *
	 * @return  object
	 */
	public function respondent()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\Respondent', 'respondent_id');
	}
}
