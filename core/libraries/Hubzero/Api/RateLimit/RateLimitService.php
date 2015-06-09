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

namespace Hubzero\Api\RateLimit;

use Hubzero\Base\Middleware;
use Hubzero\Http\Request;
use Hubzero\Utility\Date;

/**
 * Rate limit service for API
 */
class RateLimitService extends Middleware
{
	/**
	 * Load Service
	 * 
	 * @return void
	 */
	public function register()
	{
		// bind the actual rate limiter
		$this->app['ratelimiter'] = function($app)
		{
			// creat new storage object
			$storage = new Storage\Database($app['db']);

			// get rate limit config
			// json encode/decode to get as array
			$config = json_decode(json_encode($app['config']->get('rateLimit')), true);

			// create and return new rate limiter
			return new RateLimiter($storage, $config);
		};
	}

	/**
	 * Handle request in HTTP stack
	 * 
	 * @param   object  $request  HTTP Request
	 * @return  mixed
	 */
	public function handle(Request $request)
	{
		// get authentication
		$token = Auth::token();

		// rate limit application/user id and get data
		$rateLimitData = $this->app['ratelimiter']->rateLimit($token['application_id'], $token['uidNumber']);

		// get response
		$response = $this->next($request);

		// calculate header values
		$limit     = $rateLimitData->limit_short;
		$remaining = $rateLimitData->limit_short - $rateLimitData->count_short;
		$reset     = with(new Date($rateLimitData->expires_short))->toUnix();

		// if we exceeded out rate limit lets respond accordingly
		if ($rateLimitData->exceeded_long || $rateLimitData->exceeded_short)
		{
			// set response error
			$response->setError('Too Many Requests', 429, array(
				array(
					'error'             => 'rate_limit_exceeded',
					'error_description' => 'You have exceeded your rate limit allowance. Please see rate limit headers for details.'
				)
			));

			// use different values for long
			if ($rateLimitData->exceeded_long)
			{
				$limit = $rateLimitData->limit_long;
				$reset = with(new Date($rateLimitData->expires_long))->toUnix();
			}

			// always 0 if exceeded
			$remaining = 0;
		}

		// add rate limit headers
		$response->headers->set('X-RateLimit-Limit',     $limit);
		$response->headers->set('X-RateLimit-Remaining', $remaining);
		$response->headers->set('X-RateLimit-Reset',     $reset);

		// return response
		return $response;
	}
}