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
class Basic
{
    /**
     * The content of the value item
     *
     * @var  string|null
     **/
    protected $content = null;

    /**
     * Constructs the content of the value item
     *
     * @param   string  $content  The content of the given value
     * @return  void
     **/
    public function __construct($content)
    {
        $this->content = $content;
    }

    /**
     * Builds the given string representation of the value object
     *
     * @param   \Hubzero\Database\Drivers\Base\BaseSqlSyntax  $syntax  The syntax the query is being built with
     * @return  string
     **/
    public function build($syntax)
    {
        $syntax->bind(is_string($this->content) ? trim($this->content) : $this->content);

        return '?';
    }
}
