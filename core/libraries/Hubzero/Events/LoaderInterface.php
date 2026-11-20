<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * Interface for loading event groups.
 */
interface LoaderInterface
{
    /**
     * Get the event name.
     *
     * @return  string  The event name.
     */
    public function getName();

    /**
     * Load the given listener group.
     *
     * @param   string  $group
     * @return  array<mixed>
     */
    public function loadListeners($group);
}
