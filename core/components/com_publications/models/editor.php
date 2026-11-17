<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Model for a handler editor
 */
class Editor extends Obj
{
    /**
     * @var  object|null
     */
    protected $configs;

    /**
     * Handler object
     *
     * @var  object|null
     */
    public $handler = null;

    /**
     * Constructor
     *
     * @param   object  $handler
     * @param   \stdClass  $configs
     * @return  void
     */
    public function __construct($handler, $configs)
    {
        $this->handler  = $handler;
        $this->configs  = $configs;
    }

    /**
     * Draw status
     *
     * @return  string
     */
    public function drawStatus()
    {
        return $this->handler->drawStatus($this);
    }

    /**
     * Draw editor content
     *
     * @return  string
     */
    public function drawEditor()
    {
        return $this->handler->drawEditor($this);
    }
}
