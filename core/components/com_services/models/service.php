<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Services\Models;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Date;

/**
 * Service model
 *
 * @uses \Hubzero\Database\Relational
 */
class Service extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'users_points';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__users_points_services';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'id';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'alias'    => 'notempty',
		'category' => 'notempty'
	);

	/**
	 * Automatic fields to populate every time a row is updated
	 *
	 * @var  array
	 */
	public $always = array(
		'changed'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $_params = null;

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->_params))
		{
			$this->_params = new Registry($this->get('params'));
		}

		return $this->_params;
	}

	/**
	 * Generates automatic changed field value
	 *
	 * @param   array   $data  The data being saved
	 * @return  string
	 **/
	public function automaticChanged($data)
	{
		return (isset($data['changed']) && $data['changed'] ? $data['changed'] : Date::toSql());
	}

	/**
	 * Get a list of services
	 *
	 * @param   string   $category      Category
	 * @param   integer  $active        Active?
	 * @param   string   $specialgroup  Special group name
	 * @return  object
	 */
	public static function getServices($category = 'jobs', $active = 1, $specialgroup='')
	{
		$query = self::all();

		$ser = $query->getTableName();
		$grp = '#__xgroups';
		$grm = '#__xgroups_members';

		$query
			->whereEquals($ser . '.status', $active)
			->whereEquals($ser . '.category', $category)
			->order($ser . '.ordering', 'asc');

		if ($specialgroup)
		{
			$query->join($grp, $grp . '.cn', "'" . $specialgroup . "'", 'inner')
				->joinRaw($grm, $grm . '.gidNumber=' . $grp . '.gidNumber AND ' . $grm . '.uidNumber=' . \User::get('id'), 'left');
				//->whereEquals($grm . '.uidNumber', \User::get('id'));

			$query->whereEquals($ser . '.restricted', 0, 1)
				->orWhereEquals($ser . '.restricted', 1, 2)
				->where($grm . '.gidNumber', 'IS', null, 'and', 2)
				->resetDepth();
		}
		else
		{
			$query->whereEquals($ser . '.restricted', 0);
		}

		return $query->rows();
	}

	/**
	 * Load a service for a user
	 *
	 * @param   integer  $uid       User ID
	 * @param   string   $field     Field name
	 * @param   string   $category  Category
	 * @return  mixed
	 */
	public static function getUserService($uid, $field = 'alias', $category = 'jobs')
	{
		$query = self::all();

		$ser = $query->getTableName();
		$sub = Subscription::blank()->getTableName();

		$row = $query
			->join($sub, $sub . '.id', $ser . '.serviceid', 'inner')
			->whereEquals($ser . '.category', $category)
			->whereEquals($sub . '.uid', $uid)
			->order($sub . '.id', 'desc')
			->limit(1)
			->row();

		if (!$row)
		{
			$row = self::blank();
		}

		return $row->get($field);
	}
}
