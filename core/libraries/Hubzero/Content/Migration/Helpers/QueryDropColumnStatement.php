<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Helpers;

use Hubzero\Utility\Arr;

class QueryDropColumnStatement
{
    /**
     * @var  string|null
     */
    protected $asString;
    /**
     * @var  mixed
     */
    protected $name;

    /**
     * Constructs QueryDropColumnStatement instance
     *
     * @param    array<mixed>   $args   Instantiation state
     * @return   void
     */
    public function __construct($args = [])
    {
        $this->name = $args['name'];
        $this->asString = '';
    }

    /**
     * Returns string representation of drop column statement
     *
     * @return   string
     */
    public function toString()
    {
        $this->generateBaseString();
        $this->addName();

        return $this->asString;
    }

    /**
     * Generates base SQL string statement
     *
     * @return   void
     */
    protected function generateBaseString()
    {
        $this->asString = 'DROP COLUMN';
    }

    /**
     * Adds column name to SQL string statement
     *
     * @return   void
     */
    protected function addName()
    {
        $this->asString .= " $this->name";
    }
}
