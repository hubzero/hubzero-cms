<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models;

use Hubzero\Database\Relational;

/**
 * Support ticket status model
 */
class Watching extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__support_watching';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'asc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'ticket_id' => 'positive|nonzero',
		'user_id'   => 'positive|nonzero'
	);

	/**
	 * Get user
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->oneToOne('\Hubzero\User\User', 'id', 'user_id');
	}

	/**
	 * Get ticket
	 *
	 * @return  object
	 */
	public function ticket()
	{
		return $this->oneToOne(__NAMESPACE__ . '\\Ticket', 'id', 'ticket_id');
	}

	/**
	 * Get record by User ID and ticket ID
	 *
	 * @param   integer  $user_id
	 * @param   integer  $ticket_id
	 * @return  object
	 */
	public static function oneByUserAndTicket($user_id, $ticket_id)
	{
		return self::all()
			->disableCaching()
			->purgeCache()
			->whereEquals('user_id', $user_id)
			->whereEquals('ticket_id', $ticket_id)
			->row();
	}
}
