<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Auth\Link;

use Hubzero\Database\Relational;
use Date;

/**
 * Authentication Link data
 */
class Data extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'auth_link';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 */
	protected $table = '#__auth_link_data';

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
		'link_id'    => 'positive|nonzero',
		'domain_key' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 **/
	public $always = array(
		'modified'
	);

	/**
	 * Generates automatic modified field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticModified($data)
	{
		return (isset($data['modified']) && $data['modified'] ? $data['modified'] : Date::of('now')->toSql());
	}

	/**
	 * Defines a belongs to one relationship between entry and Link
	 *
	 * @return  object
	 */
	public function link()
	{
		return $this->belongsToOne('Hubzero\Auth\Link', 'link_id');
	}

	/**
	 * Get an instance of a record
	 *
	 * @param   integer  $link_id
	 * @param   string   $domain_key
	 * @return  mixed    Object on success, False on failure
	 */
	public static function oneByLinkAndKey($link_id, $domain_key)
	{
		$row = self::all()
			->whereEquals('link_id', $link_id)
			->whereEquals('domain_key', $domain_key)
			->row();

		if (!$row || !$row->get('id'))
		{
			$row = self::blank();
		}

		return $row;
	}
}
