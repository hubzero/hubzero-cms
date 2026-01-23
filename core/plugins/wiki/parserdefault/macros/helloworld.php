<?php

// phpcs:disable PSR1.Classes.ClassDeclaration.MissingNamespace
/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */


/**
 * Wiki macro class for displaying hello world
 */
class HelloWorldMacro extends WikiMacro
{
    /**
     * Returns description of macro, use, and accepted arguments
     *
     * @return     array
     */
    public function description()
    {
        $txt = array();
        $txt['wiki'] = 'Outputs "Hello world"';
        $txt['html'] = '<p>Outputs "Hello world"</p>';
        return $txt['html'];
    }

    /**
     * Generate macro output
     *
     * @return     string
     */
    public function render()
    {
        return 'Hello World, args = ' . $this->args;
    }
}
