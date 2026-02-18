<?php

// phpcs:disable PSR2.Classes.PropertyDeclaration.Underscore

/**
 * @package    hubzero-cms
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Components\Events\Models;

use Components\Tags\Models\Cloud;

/**
 * Events Tagging class
 */
class Tags extends Cloud
{
    /**
     * Object type, used for linking objects (such as resources) to tags
     *
     * @var string
     */
    protected $_scope = 'events';
}
