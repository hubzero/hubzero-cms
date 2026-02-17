<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\System\Helpers\Html;

/**
 * Utility class working with phpsetting
 */
class Phpsetting
{
    /**
     * Method to generate a boolean message for a value
     *
     * @param   boolean  $val  Is the value set?
     * @return  string   html code
     */
    public static function boolean($val)
    {
        if ($val) {
            return '<span class="state on"><span>' . Lang::txt('JON') . '</span></span>';
        }
        return '<span class="state off"><span>' . Lang::txt('JOFF') . '</span></span>';
    }

    /**
     * Method to generate a boolean message for a value
     *
     * @param   boolean  $val Is the value set?
     * @return  string   html code
     */
    public static function set($val)
    {
        if ($val) {
            return '<span class="state yes"><span>' . Lang::txt('JYES') . '</span></span>';
        }
        return '<span class="state no"><span>' . Lang::txt('JNO') . '</span></span>';
    }

    /**
     * Method to generate a string message for a value
     *
     * @param   string  $val  A php ini value
     * @return  string  html code
     */
    public static function string($val)
    {
        return (empty($val) ? Lang::txt('JNONE') : $val);
    }

    /**
     * Method to generate an integer from a value
     *
     * @param   string   $val  A php ini value
     * @return  integer
     */
    public static function integer($val)
    {
        return intval($val);
    }
}
