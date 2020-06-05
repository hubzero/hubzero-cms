<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models\Version;

use Hubzero\Database\Relational;

/**
 * Tool version zones database model
 *
 * @uses \Hubzero\Database\Relational
 */
class Zone extends Relational
{
	/**
	 * The table name
	 *
	 * @var string
	 **/
	protected $table = '#__tool_version_zone';
}
