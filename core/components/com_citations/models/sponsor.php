<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Citations\Models;

require_once __DIR__ . DS . 'citation.php';

use Hubzero\Database\Relational;

/**
 * Citation sponsor model
 *
 * @uses \Hubzero\Database\Relational
 */
class Sponsor extends Relational
{
	/**
	 * The table namespace
	 *
	 * @var  string
	 **/
	protected $namespace = 'citations';

	/**
	 * Default order by for model
	 *
	 * @var  string
	 **/
	public $orderBy = 'sponsor';

	/**
	 * Fields and their validation criteria
	 *
	 * @var  array
	 **/
	protected $rules = array(
		'name' => 'sponsor'
	);

	/**
	 * Establish relationship to citations
	 *
	 * @return  object
	 **/
	public function citations()
	{
		return $this->manyToMany('Citation', '#__citations_sponsors_assoc', 'sid', 'cid');
	}
}
