<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\View\Helper;

use Hubzero\Pagination\Paginator;
use Config;

/**
 * Create a pagination object and return it
 */
class Pagination extends AbstractHelper
{
	/**
	 * Instantiate the paginator and return it
	 *
	 * @param   integer  $total  Total number of records
	 * @param   integer  $start  Where to start
	 * @param   integer  $limit  Number of records per page
	 * @return  object
	 */
	public function __invoke($total, $start, $limit)
	{
		$start = $start ?: 0;
		$limit = $limit ?: Config::get('list_limit');

		return new Paginator($total, $start, $limit);
	}
}
