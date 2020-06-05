<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;
use Component;

require_once __DIR__ . DS . 'section.php';
require_once __DIR__ . DS . 'unit.php';

/**
 * Model class for a course offering
 */
class Offering extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 */
	protected $namespace = 'courses';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'publish_up';

	/**
	 * Default order direction for select queries
	 *
	 * @var  string
	 */
	public $orderDir = 'desc';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'title'   => 'notempty'
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
	 * Automatic fields to populate every time a row is created
	 *
	 * @var  array
	 */
	public $initiate = array(
		'created',
		'created_by'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	public $params = null;

	/**
	 * Generates automatic owned by field value
	 *
	 * @param   array   $data  the data being saved
	 * @return  string
	 */
	public function automaticAlias($data)
	{
		$alias = (isset($data['alias']) && $data['alias'] ? $data['alias'] : $data['title']);
		$alias = strip_tags($alias);
		$alias = trim($alias);
		if (strlen($alias) > 100)
		{
			$alias = substr($alias . ' ', 0, 100);
			$alias = substr($alias, 0, strrpos($alias, ' '));
		}
		$alias = str_replace(' ', '_', $alias);

		return preg_replace("/[^a-zA-Z0-9_\-\.]/", '', strtolower($alias));
	}

	/**
	 * Retrieves one row loaded by an alias field
	 *
	 * @param   string  $alias  The alias to load by
	 * @return  mixed
	 */
	public static function oneByAlias($alias)
	{
		return self::blank()
			->whereEquals('alias', $alias)
			->row();
	}

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->params))
		{
			$params = new Registry($this->get('params'));

			$p = Component::params('com_courses');
			$p->merge($params);

			$this->params = $p;
		}

		return $this->params;
	}

	/**
	 * Get parent course
	 *
	 * @return  object
	 */
	public function course()
	{
		return $this->belongsToOne('course');
	}

	/**
	 * Get sections
	 *
	 * @return  object
	 */
	public function sections()
	{
		return $this->oneToMany('Section', 'offering_id');
	}

	/**
	 * Get units
	 *
	 * @return  object
	 */
	public function units()
	{
		return $this->oneToMany('Unit', 'offering_id');
	}
}
