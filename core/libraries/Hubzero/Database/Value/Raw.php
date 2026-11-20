<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Database\Value;

/**
 * Database basic value class
 */
class Raw extends Basic
{
    /**
     * Builds the given string representation of the value object
     *
     * @param   \Hubzero\Database\Drivers\Base\BaseSqlSyntax  $syntax  The syntax the query is being built with
     * @return  string
     **/
    public function build($syntax)
    {
        return (string) $this->content;
    }
}
