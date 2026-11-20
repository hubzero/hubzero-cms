<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
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
     * @var  string
     */
    protected $name = '';

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

    /**
     * Draw an attachment the handler's way
     *
     * @param   mixed  $data
     * @param   mixed  $params
     * @return  mixed  Its HTML, if the handler has a view of its own
     */
    abstract public function drawAttachment($data, $params);

    /**
     * Draw the handler's status in the editor
     *
     * @param   Editor  $editor
     * @return  mixed
     */
    abstract public function drawStatus($editor);

    /**
     * Draw the handler's editor
     *
     * @param   Editor  $editor
     * @return  mixed
     */
    abstract public function drawEditor($editor);

    /**
     * The parent, which the calls using it need set
     *
     * @return  Handlers
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
