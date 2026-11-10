<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
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
