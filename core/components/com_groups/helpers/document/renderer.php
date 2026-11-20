<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Groups\Helpers\Document;

abstract class Renderer
{
    /**
     * @var  \Hubzero\User\Group|null
     */
    public $group = null;
    /**
     * @var  \Components\Groups\Models\Page|null
     */
    public $page = null;
    /**
     * @var  mixed
     */
    public $content = null;
    /**
     * @var  mixed
     */
    public $name    = null;
    /**
     * @var  \stdClass  The include's attributes
     */
    public $params;
    /**
     * @var  mixed
     */
    public $allMods = null;

    public function __construct() {}

    /**
     * Render the include
     *
     * @return  mixed  Its markup; script and stylesheet includes add to the document instead
     */
    abstract public function render();

    /**
     * The group, which the calls using it need set
     *
     * @return  \Hubzero\User\Group
     * @throws  \LogicException  When it is not
     */
    protected function groupOrFail()
    {
        if ($this->group === null) {
            throw new \LogicException(static::class . '::$group is not set');
        }

        return $this->group;
    }
}
