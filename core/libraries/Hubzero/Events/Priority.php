<?php

/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Events;

/**
 * An enumeration of priorities for event listeners,
 * that you are encouraged to use when adding them in the Dispatcher.
 */
final class Priority
{
    public const MIN          = -3;
    public const LOW          = -2;
    public const BELOW_NORMAL = -1;
    public const NORMAL       = 0;
    public const ABOVE_NORMAL = 1;
    public const HIGH         = 2;
    public const MAX          = 3;
}
