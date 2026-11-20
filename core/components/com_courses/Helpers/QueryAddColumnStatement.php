<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

use Hubzero\Utility\Arr;

class QueryAddColumnStatement
{
    /**
     * @var  string
     */
    protected $asString;
    /**
     * @var  mixed
     */
    protected $default;
    /**
     * @var  mixed
     */
    protected $name;
    /**
     * @var  mixed
     */
    protected $restriction;
    /**
     * @var  mixed
     */
    protected $type;

    /**
     * Constructs QueryAddColumnStatement instance
     *
     * @param    array<mixed>   $args   Instantiation state
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
