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
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2015 HUBzero Foundation, LLC.
 * @license   http://opensource.org/licenses/MIT MIT
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
	 * @return  void
	 */
	public function register()
	{
		// Bind the actual rate limiter
		$this->app['ratelimiter'] = function($app)
		{
			// creat new storage object
			$storage = new Storage\Database($app['db']);

			// Get rate limit config (JSON encode/decode to get as array)
			$config = json_decode(json_encode($app['config']->get('rate_limit')), true);
			$config = (is_array($config)) ? $config : [];

			// Create and return new rate limiter
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
		// Get response
		$response = $this->next($request);

		// Get authentication
		$token = $this->app['auth']->token();

		// Rate limit application/user id and get data
		$rateLimitData = $this->app['ratelimiter']->rateLimit($token['application_id'], $token['uidNumber']);

		// Calculate header values
		$limit     = $rateLimitData->limit_short;
		$remaining = $rateLimitData->limit_short - $rateLimitData->count_short;
		$reset     = with(new Date($rateLimitData->expires_short))->toUnix();

		// If we exceeded out rate limit lets respond accordingly
		if ($rateLimitData->exceeded_long || $rateLimitData->exceeded_short)
		{
			throw new \Exception('You have exceeded your rate limit allowance. Please see rate limit headers for details.', 429);

			// Use different values for long
			if ($rateLimitData->exceeded_long)
			{
				$limit = $rateLimitData->limit_long;
				$reset = with(new Date($rateLimitData->expires_long))->toUnix();
			}

			// Always 0 if exceeded
			$remaining = 0;
		}

		// Add rate limit headers
		$response->headers->set('X-RateLimit-Limit',     $limit);
		$response->headers->set('X-RateLimit-Remaining', $remaining);
		$response->headers->set('X-RateLimit-Reset',     $reset);

		// Return response
		return $response;
	}
}