<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */
namespace Components\Tools\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Tool file handlers database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Rule extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'tool_handler';

	/**
	 * Fields and their validation criteria
	 *
	 * @var array
	 **/
	protected $rules = array(
		'handler_id' => 'notempty|nonzero',
		'extension'  => 'notempty',
		'quantity'   => 'notempty'
	);

	/**
	 * Defines the inverse relationship between a handler and a tool
	 *
	 * @return \Hubzero\Database\Relationship\belongsToOne
	 **/
	public function handler()
	{
		return $this->belongsToOne('Handler');
	}
}
