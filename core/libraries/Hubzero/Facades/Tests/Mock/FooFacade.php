<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Facades\Tests\Mock;

use Hubzero\Facades\Facade;

/**
 * Mock Foo facade
 *
 * @method static string bar()
 * @method static int    multiArg(mixed ...$args)
 *
 * @codeCoverageIgnore
 */
class FooFacade extends Facade
{
    /**
     * Get the registered name.
     *
     * @return  string
     */
    protected static function getAccessor()
    {
        return 'foo';
    }
}
