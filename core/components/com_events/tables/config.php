<?php
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Lang;

include_once __DIR__ . DS . 'configs.php';

/**
 * Events table class for configuration
 */
class Config extends Table
{
	/**
	 * Constructor
	 *
	 * @param   object  &$db  Database
	 * @return  void
	 */
	public function __construct(&$db)
	{
		parent::__construct('#__events_config', 'param', $db);
	}

	/**
	 * Validate data
	 *
	 * @return  boolean  True if data is valid
	 */
	public function check()
	{
		// check for valid name
		if (trim($this->param) == '')
		{
			$this->setError(Lang::txt('EVENTS_BLANK_CONFIG_PARAMETER'));
			return false;
		}
		return true;
	}
}
