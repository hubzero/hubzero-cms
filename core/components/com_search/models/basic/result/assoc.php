<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Search\Models\Basic\Result;

use Components\Search\Models\Basic\Result as SearchResult;

/**
 * Associative result
 */
abstract class Assoc extends SearchResult
{

	/**
	 * Is the result a scalar?
	 *
	 */
	abstract public function is_scalar();
}
