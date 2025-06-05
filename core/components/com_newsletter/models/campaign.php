<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2023 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Newsletter\Models;

use Hubzero\Database\Relational;
use Date;
use User;

/**
 * Model for a campaign
 */
class Campaign extends Relational
{
	/**
	 * The table name
	 *
	 * @var  string
	 */
	protected $table = '#__campaign';

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
		'title' => 'notempty'
	);
	/**
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'campaign_date',
		'secret'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified',
		'modified_by',
	);

	/**
	 * Generates automatic current date field value for campaign_date
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticCampaignDate($data)
	{
		return Date::of('now')->toSql();
	}

	/**
	 * Generates automatic modified by field value
	 *
	 * @param   array    $data  the data being saved
	 * @return  integer
	 */
	public function automaticModifiedBy($data)
	{
		return User::get('id');
	}

	/**
	 * Generates automatic created field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return $this->automaticCampaignDate($data);
	}

	/**
	 * Generates automatic secret value on creation of new record
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticSecret($data)
	{
		return $this->generateSecret($data);
	}

	/**
	 * Generates new secret value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public static function generateSecret($data)
	{
		// create 32-character secret:
		$secretLength = 32;
		return \Hubzero\User\Password::genRandomPassword($secretLength);
	}

	public function isExpired()
	{
		$expiration = $this->get('expire_date');

		$invalidExpiration = empty($expiration);
		$isExpired = strtotime(Date::of()) > strtotime($expiration ?: '9999-12-31 23:59:59');

		return $invalidExpiration || $isExpired;
	}
}
