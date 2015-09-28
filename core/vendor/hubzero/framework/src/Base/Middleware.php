<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   framework
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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