<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication handler model class
 */
abstract class Handler extends Obj
{
    /**
     * Handler name
     *
     * This has to be set in the final
     * renderer classes.
     *
     * @var  string|null
     */
    protected $name = null;

    /**
     * Reference to the object that instantiated the element
     *
     * @var  Handlers|null
     */
    protected $parent = null;

    /**
     * Constructor
     *
     * @param   Handlers|null  $parent
     * @return  void
     */
    public function __construct($parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Get the element name
     *
     * @return  string  type of the parameter
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Get the handler's configuration, merged with what was saved
     *
     * @param   array<mixed>  $savedConfig
     * @return  mixed
     */
    abstract public function getConfig($savedConfig = []);

    /**
     * Check that the attachments meet the handler's requirements
     *
     * @param   array<mixed>  $attachments
     * @return  bool
     */
    abstract public function checkRequired($attachments);
}
