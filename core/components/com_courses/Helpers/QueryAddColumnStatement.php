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
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $asString;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $default;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $name;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $restriction;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
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

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function generateBaseString()
    {
        $this->asString = 'ADD COLUMN';
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addName()
    {
        $this->asString .= " $this->name";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addType()
    {
        $this->asString .= " $this->type";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addRestriction()
    {
        $restriction = rtrim(" $this->restriction");

        $this->asString .= $restriction;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addDefault()
    {
        $default = '';

        if ($this->default === 0 || $this->default) {
            $default = " DEFAULT $this->default";
        }

        $this->asString .= $default;
    }
}
