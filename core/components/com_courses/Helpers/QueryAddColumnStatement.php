<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

use Hubzero\Utility\Arr;

class QueryAddColumnStatement
{
    protected $asString;
    protected $default;
    protected $name;
    protected $restriction;
    protected $type;

    /**
     * Constructs QueryAddColumnStatement instance
     *
     * @param    array   $args   Instantiation state
     * @return   void
     */
    public function __construct($args = [])
    {
        $this->name = $args['name'];
        $this->type = $args['type'];
        $this->restriction = Arr::getValue($args, 'restriction', null);
        $this->default = Arr::getValue($args, 'default', null);
        $this->asString = '';
    }

    /**
     * Returns string representation of add column statement
     *
     * @return   string
     */
    public function toString()
    {
        $this->generateBaseString();
        $this->addName();
        $this->addType();
        $this->addRestriction();
        $this->addDefault();

        return $this->asString;
    }

    /** @return void */
    protected function generateBaseString()
    {
        $this->asString = 'ADD COLUMN';
    }

    /** @return void */
    protected function addName()
    {
        $this->asString .= " $this->name";
    }

    /** @return void */
    protected function addType()
    {
        $this->asString .= " $this->type";
    }

    /** @return void */
    protected function addRestriction()
    {
        $restriction = rtrim(" $this->restriction");

        $this->asString .= $restriction;
    }

    /** @return void */
    protected function addDefault()
    {
        $default = '';

        if ($this->default === 0 || $this->default) {
            $default = " DEFAULT $this->default";
        }

        $this->asString .= $default;
    }
}
