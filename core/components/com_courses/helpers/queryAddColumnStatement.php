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
    protected $_asString;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_default;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_restriction;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_type;

    /**
     * Constructs QueryAddColumnStatement instance
     *
     * @param    array   $args   Instantiation state
     * @return   void
     */
    public function __construct($args = [])
    {
        $this->_name = $args['name'];
        $this->_type = $args['type'];
        $this->_restriction = Arr::getValue($args, 'restriction', null);
        $this->_default = Arr::getValue($args, 'default', null);
        $this->_asString = '';
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

        return $this->_asString;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _generateBaseString()
    {
        $this->_asString = 'ADD COLUMN';
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addName()
    {
        $this->_asString .= " $this->_name";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addType()
    {
        $this->_asString .= " $this->_type";
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addRestriction()
    {
        $restriction = rtrim(" $this->_restriction");

        $this->_asString .= $restriction;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addDefault()
    {
        $default = '';

        if ($this->_default === 0 || $this->_default) {
            $default = " DEFAULT $this->_default";
        }

        $this->_asString .= $default;
    }
}
