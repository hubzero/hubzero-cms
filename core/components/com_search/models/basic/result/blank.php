<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;
use Exception;

include_once dirname(__DIR__) . DS . 'result.php';

/**
 * Empty result
 */
class Blank extends SearchResult
{
	/**
	 * Return results as associative array
	 *
	 * @return  void
	 * @throws  Exception  Exception description (if any) ...
	 */
	public function to_associative()
	{
		throw new Exception('empty result -> to_associative');
	}
}
