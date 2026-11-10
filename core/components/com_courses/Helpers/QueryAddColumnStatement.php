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
        $this->_generateBaseString();
        $this->_addName();
        $this->_addType();
        $this->_addRestriction();
        $this->_addDefault();

        return $this->asString;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _generateBaseString()
    {
        $this->asString = 'ADD COLUMN';
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addName()
    {
        $this->asString .= " $this->name";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addType()
    {
        $this->asString .= " $this->type";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addRestriction()
    {
        $restriction = rtrim(" $this->restriction");

        $this->asString .= $restriction;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addDefault()
    {
        $default = '';

        if ($this->default === 0 || $this->default) {
            $default = " DEFAULT $this->default";
        }

        $this->asString .= $default;
    }
}
