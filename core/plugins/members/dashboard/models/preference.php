<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Plugins\Members\Dashboard\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

/**
 * Model class for dashboard preferences
 */
class Preference extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'xprofiles_dashboard';

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
		'preferences' => 'notempty',
		'uidNumber'   => 'positive|nonzero'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'modified'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $preferences = null;

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return Date::toSql();
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string  $user_id  The alias to load by
	 * @return  mixed
	 */
	public static function oneByUser($user_id)
	{
		return self::blank()
			->whereEquals('uidNumber', $user_id)
			->row();
	}

	/**
	 * Get the parent user associated with this entry
	 *
	 * @return  object
	 */
	public function user()
	{
		return $this->belongsToOne('Hubzero\User\User', 'uidNumber');
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformPreferences()
	{
		if (!is_object($this->preferences))
		{
			$this->preferences = new Registry($this->get('preferences'));
		}

		return $this->preferences;
	}

	/**
	 * Save data
	 *
	 * @return  bool
	 */
	public function save()
	{
		if (!is_string($this->get('preferences')))
		{
			$this->set('preferences', json_encode($this->get('preferences')));
		}

		return parent::save();
	}
}
