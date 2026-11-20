<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Publications\Models;

use Hubzero\Base\Obj;

/**
 * Publication block element class
 */
class BlockElement extends Obj
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
     * Reference to the object that instantiated the element
     *
     * @var  BlockElements|null
     */
    protected $parent = null;

    /**
     * Constructor
     *
     * @param   BlockElements|null  $parent
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
     * The parent, which the calls using it need set
     *
     * @return  BlockElements
     * @throws  \LogicException  When it is not
     */
    protected function parentOrFail()
    {
        if ($this->parent === null) {
            throw new \LogicException(static::class . '::$parent is not set');
        }

        return $this->parent;
    }
}
