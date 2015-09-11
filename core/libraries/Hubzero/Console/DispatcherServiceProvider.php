<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 HUBzero Foundation, LLC.
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
 * @author    Sam Wilson <samwilson@purdue.edu>
 * @copyright Copyright 2005-2015 HUBzero Foundation, LLC.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

namespace Hubzero\Console;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;

/**
 * Console dispatcher service provider
 */
class DispatcherServiceProvider extends Middleware
{
	/**
	 * Register the service provider.
	 *
	 * @return  void
	 */
	public function register()
	{
	}

	/**
	 * Handle request in stack
	 * 
	 * @param   object  $request  Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		$response = $this->next($request);

		$class = $this->app->get('arguments')->get('class');
		$task  = $this->app->get('arguments')->get('task');

		$command   = new $class($this->app->get('output'), $this->app->get('arguments'));
		$shortName = strtolower(with(new \ReflectionClass($command))->getShortName());

		// Fire default before event
		Event::fire($shortName . '.' . 'before' . ucfirst($task));

		$command->{$task}();

		// Fire default after event
		Event::fire($shortName . '.' . 'after' . ucfirst($task));

		$this->app->get('output')->render();

		return $response;
	}
}