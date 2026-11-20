<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publications block base class
 */
class Block extends Obj
{
    /**
     * Element name
     *
     * This has to be set in the final
     * renderer classes.
     *
     * @var  string
     */
    protected $name = '';

    /**
     * Block manifest
     *
     * This has to be set in the final
     * renderer classes.
     *
     * @var  \stdClass
     */
    protected $manifest;

    /**
     * Reference to the object that instantiated the element
     *
     * @var  Blocks
     */
    protected $parent;

    /**
     * Constructor
     *
     * @param   Blocks  $parent
     * @return  void
     */
    public function __construct($parent)
    {
        $this->parent = $parent;
    }

    /**
     * Get the block name
     *
     * @return  string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get property
     *
     * @param   string  $name
     * @return  mixed
     */
    public function getProperty($name)
    {
        if (isset($this->$name)) {
            return $this->$name;
        }

        return false;
    }
}
