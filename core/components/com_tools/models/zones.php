<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright 2005-2019 HUBzero Foundation, LLC.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Tools\Models;

use Components\Tools\Helpers\Utils;
use Hubzero\Base\Obj;

/**
 * Zones Model for Tools Component
 */
class Zones extends Obj
{
	/**
	 * Get execution zones list
	 *
	 * @return  array
	 */
	public function getExecutionZones()
	{
		$query = "SELECT zone FROM `zones`";

		$mwdb = Utils::getMWDBO();
		$mwdb->setQuery($query);

		return $mwdb->loadList();
	}
}
