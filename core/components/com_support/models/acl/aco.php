<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models\Acl;

use Hubzero\Database\Relational;

/**
 * Support ticket ACO model
 */
class Aco extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support_acl';

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
		'model'       => 'notempty',
		'foreign_key' => 'positive|nonzero'
	);

	/**
	 * Get maps
	 *
	 * @return  object
	 */
	public function maps()
	{
		return $this->oneToMany(__NAMESPACE__ . '\\Map', 'aco_id');
	}

	/**
	 * Delete the record and all associated data
	 *
	 * @return  boolean  False if error, True on success
	 */
	public function destroy()
	{
		// Remove data
		foreach ($this->maps()->rows() as $map)
		{
			if (!$map->destroy())
			{
				$this->addError($map->getError());
				return false;
			}
		}

		// Attempt to delete the record
		return parent::destroy();
	}
}
