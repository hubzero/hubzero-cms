<?php

/**
 * @package    framework
 * @copyright  Copyright © 2005-2026 Purdue University. All Rights Reserved.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Container;

/**
 * Container service provider interface.
 *
 * Inspired by Fabien Potencier's Pimple DI class
 */
interface ServiceProviderInterface
{
    /**
     * Registers services on the given container.
     *
     * This method should only be used to configure services and parameters.
     * It should not get services.
     *
     * @param  \Hubzero\Container\Container $container  A Container instance
     * @return void
     */
    public function register(Container $container);
}
