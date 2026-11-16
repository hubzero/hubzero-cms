<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Cart\Lib\Handlers;

class ModelHandler
{
    // Database instance
    /**
     * @var  mixed
     */
    public $db = null;

    // Item info
    /**
     * @var  array<string, mixed>|null
     */
    public $item;

    /**
     * @var  int|null
     */
    public $crtId;
    /**
     * @var  int|null
     */
    public $tId;

    /**
     * Constructor
     *
     * @param   array<string, mixed>  $item
     * @param   int  $crtId
     * @param   int  $tId
     */
    public function __construct($item, $crtId, $tId)
    {
        $this->item = $item;
        $this->crtId = $crtId;
        $this->tId = $tId;
    }
}
