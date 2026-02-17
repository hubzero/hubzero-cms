<?php

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Wiki\Models;

use Components\Tags\Models\Cloud;

/**
 * Wiki Tagging class
 */
class Tags extends Cloud
{
    /**
     * Object type, used for linking objects (such as resources) to tags
     *
     * @var string
     */
    // phpcs:ignore PSR2.Classes.PropertyDeclaration.Underscore
    protected $_scope = 'wiki';
}
