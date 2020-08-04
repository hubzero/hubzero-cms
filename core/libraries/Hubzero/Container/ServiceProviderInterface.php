<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
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
	 * @param  object  $container  A Container instance
	 */
	public function register(Container $container);
}
