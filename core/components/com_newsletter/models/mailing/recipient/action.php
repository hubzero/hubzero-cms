<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models\Mailing\Recipient;

use Hubzero\Database\Relational;

/**
 * Newsletter model for a mailing recipient action
 */
class Action extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'newsletter_mailing_recipient';

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
		'action'    => 'notempty',
		'mailingid' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between mailing and recipient
	 *
	 * @return  object
	 */
	public function mailing()
	{
		return $this->belongsToOne('Components\\Newsletter\\Models\\Mailing', 'mailingid');
	}

	/**
	 * Load a record by mailing ID, email, and action
	 *
	 * @param   integer  $mailingid
	 * @param   string   $email
	 * @param   string   $action
	 * @return  object
	 */
	public static function oneForMailingAndEmail($mailingid, $email, $action)
	{
		$row = self::all()
			->whereEquals('mailingid', $mailingid)
			->whereEquals('email', $email)
			->whereEquals('action', $action)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row;
	}
}
