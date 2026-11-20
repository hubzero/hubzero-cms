<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Dataviewer\Admin\Libs;

use Components\Dataviewer\Admin\DvConfig;

class Security
{
    /**
     * @return  true
     */
    public static function checkRid()
    {
        if (isset($_POST[DvConfig::$rid]) && $_POST[DvConfig::$rid] == DvConfig::$rid) {
            return true;
        }

        exit();
    }

    /**
     * @param   string  $value
     * @param   string  $label
     * @return  string
     */
    public static function dvIdentifier($value, $label = 'identifier')
    {
        $value = (string) $value;

        if ($value === '' || !preg_match('/^[A-Za-z0-9_.-]+$/', $value) || str_contains($value, '..')) {
            \App::abort(400, 'Invalid ' . $label);
        }

        return $value;
    }
}
