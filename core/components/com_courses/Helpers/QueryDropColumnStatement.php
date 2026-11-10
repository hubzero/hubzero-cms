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
    protected $asString;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $name;

    /**
     * Constructs QueryDropColumnStatement instance
     *
     * @param    array   $args   Instantiation state
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
        $this->_generateBaseString();
        $this->_addName();

        return $this->asString;
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _generateBaseString()
    {
        $this->asString = 'DROP COLUMN';
    }

    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function _addName()
    {
        $this->asString .= " $this->name";
    }
}
