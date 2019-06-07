<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models\Orm;

use Hubzero\Database\Relational;
use Hubzero\Config\Registry;

/**
 * Model class for publication category
 */
class Category extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'publication';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 */
	protected $rules = array(
		'name'      => 'notempty',
		'alias'     => 'notempty',
		'url_alias' => 'notempty'
	);

	/**
	 * Configuration registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Is this entry contributable
	 *
	 * @return  boolean
	 */
	public function isContributable()
	{
		return ($this->get('contributable') == 1);
	}

	/**
	 * Is this entry used?
	 *
	 * @return  boolean
	 */
	public function isUsed()
	{
		require_once __DIR__ . DS . 'publication.php';

		$total = Publication::all()
			->whereEquals('category', $this->get('id'))
			->total();

		return ($total > 0);
	}

	/**
	 * Get params as a Registry object
	 *
	 * @return  object
	 */
	public function transformParams()
	{
		if (!($this->paramsRegistry instanceof Registry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}
		return $this->paramsRegistry;
	}

	/**
	 * Get contributable categories
	 *
	 * @return  object
	 */
	public static function contributable()
	{
		return self::all()
			->whereEquals('contributable', 1);
	}
}
