<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

use Hubzero\Database\Relational;

/**
 * Citation association model
 *
 * @uses \Hubzero\Database\Relational
 */
class Association extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var string
	 **/
	protected $namespace = 'citations';

	/**
	 * The table name, non-standard naming 
	 *
	 * @var  string
	 */
	protected $table = '#__citations_assoc';

	/**
	 * Default order by for model
	 *
	 * @var string
	 **/
	public $orderBy = 'id';

	/**
	 * Establish relationship to citation
	 *
	 * @return  object
	 **/
	public function citation()
	{
		return $this->belongsToOne('Citation', 'cid');
	}
}
