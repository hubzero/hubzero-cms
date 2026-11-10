<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $name = null;

    /**
     * Block manifest
     *
     * This has to be set in the final
     * renderer classes.
     *
     * @var  string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $manifest = null;

    /**
     * Reference to the object that instantiated the element
     *
     * @var  object
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $parent = null;

    /**
     * Constructor
     *
     * @param   object  $parent
     * @return  void
     */
    public function __construct($parent = null)
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
