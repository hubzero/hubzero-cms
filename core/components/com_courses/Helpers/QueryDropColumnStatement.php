<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

class QueryDropColumnStatement
{
    /**
     * @var  string
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

    /** @return void */
    protected function generateBaseString()
    {
        $this->asString = 'DROP COLUMN';
    }

    /** @return void */
    protected function addName()
    {
        $this->asString .= " $this->name";
    }
}
