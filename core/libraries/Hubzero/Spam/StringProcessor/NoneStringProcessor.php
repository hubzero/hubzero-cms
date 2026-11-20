<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Spam\StringProcessor;

/**
 * None string processor. Does nothing on the string
 */
class NoneStringProcessor implements StringProcessorInterface
{
    /**
     * Prepare a string
     *
     * @param   string  $string
     * @return  string
     */
    public function prepare($string)
    {
        return $string;
    }
}
