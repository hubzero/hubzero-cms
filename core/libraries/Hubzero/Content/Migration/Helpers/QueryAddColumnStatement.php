<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Content\Migration\Helpers;

use Hubzero\Utility\Arr;

class QueryAddColumnStatement
{
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $asString;
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $defaultValue;
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
        $this->defaultValue = Arr::getValue($args, 'default', null);
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

    /**
     * Generates base SQL string statement
     *
     * @return   void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function generateBaseString()
    {
        $this->asString = 'ADD COLUMN';
    }

    /**
     * Adds column name to SQL string statement
     *
     * @return   void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addName()
    {
        $this->asString .= " $this->name";
    }

    /**
     * Adds column type to SQL string statement
     *
     * @return   void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addType()
    {
        $this->asString .= " $this->type";
    }

    /**
     * Adds column restriction to SQL string statement
     *
     * @return   void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addRestriction()
    {
        $restriction = rtrim(" $this->restriction");

        $this->asString .= $restriction;
    }

    /**
     * Adds column default to SQL string statement
     *
     * @return   void
     */
    // phpcs:ignore PSR2.Methods.MethodDeclaration.Underscore
    protected function addDefault()
    {
        $default = '';

        if ($this->defaultValue === 0 || $this->defaultValue) {
            $default = " DEFAULT $this->defaultValue";
        }

        $this->asString .= $default;
    }
}
