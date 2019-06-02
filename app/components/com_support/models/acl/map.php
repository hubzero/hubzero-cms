<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Support\Models\Acl;

use Hubzero\Database\Relational;

include_once __DIR__ . '/aco.php';
include_once __DIR__ . '/aro.php';

/**
 * Support ticket ACL map model
 */
class Map extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	public $namespace = 'support_acl';

	/**
	 * The table to which the class pertains
	 *
	 * This will default to #__{namespace}_{modelName} unless otherwise
	 * overwritten by a given subclass. Definition of this property likely
	 * indicates some derivation from standard naming conventions.
	 *
	 * @var  string
	 **/
	protected $table = '#__support_acl_aros_acos';

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
		'aco_id' => 'positive|nonzero',
		'aro_id' => 'positive|nonzero'
	);

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function aco()
	{
		return $this->belongsToOne('Aco', 'aco_id');
	}

	/**
	 * Defines a belongs to one relationship between comment and user
	 *
	 * @return  object
	 */
	public function aro()
	{
		return $this->belongsToOne('Aro', 'aro_id');
	}

	/**
	 * Can create?
	 *
	 * @return  bool
	 */
	public function canCreate()
	{
		return ($this->get('action_create') == 1);
	}

	/**
	 * Can read?
	 *
	 * @return  bool
	 */
	public function canRead()
	{
		return ($this->get('action_read') == 1);
	}

	/**
	 * Can update?
	 *
	 * @return  bool
	 */
	public function canUpdate()
	{
		return ($this->get('action_update') == 1);
	}

	/**
	 * Can delete?
	 *
	 * @return  bool
	 */
	public function canDelete()
	{
		return ($this->get('action_delete') == 1);
	}
}
