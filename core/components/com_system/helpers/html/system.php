<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Helpers\Html;

use Hubzero\Facades\Lang;

/**
 * Utility class working with system
 */
class System
{
    /**
     * Method to generate a string message for a value
     *
     * @param   string  $val  A php ini value
     * @return  string  html code
     */
    public static function server($val)
    {
        return (empty($val) ? Lang::txt('COM_SYSTEM_INFO_NA') : $val);
    }
}
