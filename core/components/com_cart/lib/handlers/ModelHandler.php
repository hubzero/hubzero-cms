<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

// phpcs:ignore PSR1.Classes.ClassDeclaration.MissingNamespace, Squiz.Classes.ValidClassName.NotCamelCaps
class Model_Handler
{
    // Database instance
    public $db = null;

    // Item info
    public $item;

    public $crtId;
    public $tId;

    /**
     * Constructor
     *
     */
    public function __construct($item, $crtId, $tId)
    {
        $this->item = $item;
        $this->crtId = $crtId;
        $this->tId = $tId;
    }
}
