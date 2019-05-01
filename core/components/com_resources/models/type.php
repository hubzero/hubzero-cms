<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Resources\Models;

use Components\Resources\Models\Author\Role\Type as RoleType;
use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

include_once __DIR__ . DS . 'author' . DS . 'role.php';

/**
 * Resource type model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'resource';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 */
	public $orderBy = 'type';

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
		'type' => 'notempty'
	);

	/**
	 * Automatically fillable fields
	 *
	 * @var  array
	 */
	public $always = array(
		'alias'
	);

	/**
	 * Params Registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['type']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 70)
		{
			$alias = substr($alias . ' ', 0, 70);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '-', $alias);

		return preg_replace("/[^a-zA-Z0-9\-]/", '', strtolower($alias));
	}

	/**
	 * Transform params
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!is_object($this->paramsRegistry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}

		return $this->paramsRegistry;
	}

	/**
	 * Get a list of roles for this type
	 *
	 * @return  object
	 */
	public function roles()
	{
		$model = new RoleType();
		return $this->manyToMany(__NAMESPACE__ . '\\Author\\Role', $model->getTableName(), 'type_id', 'role_id');
	}

	/**
	 * Is this the tool type?
	 *
	 * @return  bool
	 */
	public function isForTools()
	{
		return ($this->get('id') == 7);
	}

	/**
	 * Get major types
	 *
	 * @return  object
	 */
	public static function getMajorTypes()
	{
		return self::all()
			->whereEquals('category', 27)
			->ordered()
			->rows();
	}
}
