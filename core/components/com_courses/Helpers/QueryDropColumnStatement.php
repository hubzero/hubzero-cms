<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Courses\Helpers;

use Hubzero\Utility\Arr;

class QueryDropColumnStatement
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_asString;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_name;

    /**
     * Constructs QueryDropColumnStatement instance
     *
     * @param    array   $args   Instantiation state
     * @return   void
     */
    public function __construct($args = [])
    {
        $this->_name = $args['name'];
        $this->_asString = '';
    }

    /**
     * Returns string representation of drop column statement
     *
     * @return   string
     */
    public function toString()
    {
        $this->_generateBaseString();
        $this->_addName();

        return $this->_asString;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _generateBaseString()
    {
        $this->_asString = 'DROP COLUMN';
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addName()
    {
        $this->_asString .= " $this->_name";
    }
}
