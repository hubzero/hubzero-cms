<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wishlist\Models;

use Hubzero\Database\Relational;
use Lang;
use Date;

/**
 * Wishlist class for a wish plan model
 */
class Plan extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'wishlist';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__wishlist_implementation';

	/**
	 * Default order by for model
	 *
	 * @var string
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
		'pagetext' => 'notempty',
		'wishid'   => 'positive|nonzero'
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
	 * Fields to be parsed
	 *
	 * @var  array
	 */
	protected $parsed = array(
		'pagetext'
	);

	/**
	 * Get the owning wish
	 *
	 * @return  object
	 */
	public function wish()
	{
		return $this->belongsToOne(__NAMESPACE__ . '\\Wish', 'wishid');
	}

	/**
	 * Get the creator of this entry
	 *
	 * @return  object
	 */
	public function creator()
	{
		return $this->belongsToOne('Hubzero\User\User', 'created_by');
	}

	/**
	 * Return a formatted timestamp
	 *
	 * @param   string  $rtrn  What data to return
	 * @return  string
	 */
	public function created($rtrn='')
	{
		$rtrn = strtolower($rtrn);

		if ($rtrn == 'date')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('DATE_FORMAT_HZ1'));
		}

		if ($rtrn == 'time')
		{
			return Date::of($this->get('created'))->toLocal(Lang::txt('TIME_FORMAT_HZ1'));
		}

		return $this->get('created');
	}

	/**
	 * Parses content string as directed
	 *
	 * @return  string
	 */
	public function transformContent()
	{
		$field = 'about';

		$property = "_{$field}Parsed";

		if (!isset($this->$property))
		{
			$params = array(
				'option'   => 'com_wishlist',
				'scope'    => 'wishlist',
				'pagename' => 'wishlist',
				'pageid'   => $this->get('wishid'),
				'filepath' => '',
				'domain'   => $this->get('wishid')
			);

			$this->$property = Html::content('prepare', $this->get($field, ''), $params);
		}

		return $this->$property;
	}
}
