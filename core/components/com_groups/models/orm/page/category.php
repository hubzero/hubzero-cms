<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Models\Orm\Page;

use Hubzero\Database\Relational;

/**
 * Group page category model
 */
class Category extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'xgroups_pages';

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
		'gidNumber' => 'positive|nonzero',
		'title'     => 'notempty'
	);

	/**
	 * Get parent group
	 *
	 * @return  object
	 */
	public function group()
	{
		return $this->belongsToOne('Components\Groups\Models\Orm\Group', 'gidNumber');
	}

	/**
	 * Get pages assigned to this category
	 *
	 * @return  object
	 */
	public function pages()
	{
		return $this->oneToMany('Components\Groups\Models\Orm\Page', 'category');
	}
}
