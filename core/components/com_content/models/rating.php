<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Content\Models;

use Hubzero\Database\Relational;

/**
 * Model class for a rating
 */
class Rating extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 */
	protected $namespace = 'content';

	/**
	 * The table name
	 *
	 * @var  string
	 */
	protected $table = '#__content_rating';

	/**
	 * The table primary key name
	 *
	 * It defaults to 'id', but can be overwritten by a subclass.
	 *
	 * @var  string
	 **/
	protected $pk = 'content_id';

	/**
	 * Default order by for model
	 *
	 * @var string
	 */
	public $orderBy = 'content_id';

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
		'content_id' => 'positive|nonzero'
	);
}
