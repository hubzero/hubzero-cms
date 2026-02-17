<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

/**
 * Product type handler. Parent class for all type handlers.
 */
// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps
class Type_Handler
{
    // Database instance
    public $db = null;

    // Item info
    public $item;

    // Cart ID
    public $crtId;

    /**
     * Constructor
     *
     */
    public function __construct($item, $crtId)
    {
        $this->item = $item;
        $this->crtId = $crtId;
    }
}
