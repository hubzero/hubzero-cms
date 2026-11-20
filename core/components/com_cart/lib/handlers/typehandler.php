<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers;

/**
 * Product type handler. Parent class for all type handlers.
 */
class TypeHandler
{
    // Database instance
    /**
     * @var  \Hubzero\Database\Driver
     */
    public $db;

    // Item info
    /**
     * @var  array<string, mixed>
     */
    public $item;

    // Cart ID
    /**
     * @var  int
     */
    public $crtId;

    /**
     * Constructor
     *
     * @param   array<string, mixed>  $item
     * @param   int  $crtId
     */
    public function __construct($item, $crtId)
    {
        $this->item = $item;
        $this->crtId = $crtId;
    }
}
