<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
 */

namespace Hubzero\Base;

use Hubzero\Base\ServiceProvider;
use Hubzero\Http\Request;

/**
 * App Middleware
 *
 * Inspired, in part, by Laravel
 * http://laravel.com
 */
abstract class Middleware extends ServiceProvider
{
	/**
	 * Call next service
	 *
	 * This under the hood resolves the stack on the api container
	 * and then calls next on it passing the request object. The stack
	 * object holds onto the current position it is within the stack so
	 * each service doesnt have to know anything about what comes before
	 * or after it.
	 *
	 * @param   object  $request  Request object
	 * @return  mixed   Result of next runnable service
	 */
	public function next(Request $request)
	{
		return $this->app['stack']->next($request);
	}

	/**
	 * Handle request object
	 *
	 * Each runnable service must implement this method and do what it wants
	 * and then MUST pass the request along to the next service after its done.
	 *
	 * @param   object  $request  Request object
	 * @return  mixed   Result
	 */
	abstract public function handle(Request $request);
}
