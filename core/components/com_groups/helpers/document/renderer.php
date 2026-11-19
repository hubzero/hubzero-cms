<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
    public $page    = null;
    /**
     * @var  mixed
     */
    public $content = null;
    /**
     * @var  mixed
     */
    public $name    = null;
    /**
     * @var  \stdClass|null  The include's attributes
     */
    public $params  = null;
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
}
