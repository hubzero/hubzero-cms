<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Tests\Mock;

use Hubzero\Database\Nested;

/**
 * Discussion mock model
 *
 * @uses  \Hubzero\Database\Nested
 */
class Discussion extends Nested
{
	/**
	 * Scopes to limit the realm of the nested set functions
	 *
	 * @var  array
	 **/
	protected $scopes = ['scope', 'scope_id'];
}
