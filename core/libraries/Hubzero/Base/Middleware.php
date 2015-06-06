<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Base;

use Hubzero\Base\ServiceProvider;
use Hubzero\Http\Request;

/**
 * App Middleware
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