<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models\Orm;

use Hubzero\Database\Relational;

/**
 * Projects type model
 *
 * @uses  \Hubzero\Database\Relational
 */
class Type extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'project';

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
		'type' => 'notempty'
	);

	/**
	 * Registry
	 *
	 * @var  object
	 */
	protected $paramsRegistry = null;

	/**
	 * Transform params
	 *
	 * @return  string
	 */
	public function transformParams()
	{
		if (!is_object($this->paramsRegistry))
		{
			$this->paramsRegistry = new Registry($this->get('params'));
		}

		return $this->paramsRegistry;
	}
}
