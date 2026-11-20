<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Form\Exception;

class InvalidData extends \Exception
{
    /**
     * Returns to error message
     *
     * @return  string  Error message
     */
    public function __toString()
    {
        return $this->getMessage();
    }

    /**
     * Returns to error message
     *
     * @return  string  Error message
     */
    public function toString()
    {
        return $this->__toString();
    }
}
