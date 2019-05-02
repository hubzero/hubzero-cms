<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
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
