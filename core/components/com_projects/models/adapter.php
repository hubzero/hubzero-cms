<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Projects\Models;

use Hubzero\Base\Obj;

/**
 * Project File Adapter model
 */
class Adapter extends Obj
{
    /**
     * Exec call
     *
     * @param   string  $call
     * @return  mixed   to be parsed
     */
    protected function exec($call = '')
    {
        if (!$call) {
            return false;
        }

        $result = [];

        // exec call
        exec($call, $result);
        return $result;
    }
}
