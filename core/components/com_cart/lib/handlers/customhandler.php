<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers;

/**
 * Custom handler. Parent class for all custom handlers.
 */
class CustomHandler
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
