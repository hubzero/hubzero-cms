<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Tables;

use Hubzero\Database\Table;
use Hubzero\Facades\Lang;

/**
 * Events table class for configuration
 *
 * @property  mixed  $param
 * @property  mixed  $value
 */
class Config extends Table
{
    /**
     * Constructor
     *
     * @param   \Hubzero\Database\Driver  &$db  Database
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
        if (trim($this->param) == '') {
            $this->setError(Lang::txt('EVENTS_BLANK_CONFIG_PARAMETER'));
            return false;
        }
        return true;
    }
}
