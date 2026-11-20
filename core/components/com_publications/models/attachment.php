<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication attachment model class
 */
class Attachment extends Obj
{
    /**
     * @var  mixed
     */
    protected $connector;

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
     * Reference to the object that instantiated the element
     *
     * @var  Attachments
     */
    protected $parent;

    /**
     * Constructor
     *
     * @param   Attachments  $parent
     * @return  void
     */
    public function __construct($parent)
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
     * Get the element connector property
     *
     * @return  string  type of the parameter
     */
    public function getConnector()
    {
        return $this->connector;
    }
}
