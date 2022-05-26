<?php
/**
 * @package    framework
 * @copyright  Copyright (c) 2005-2020 The Regents of the University of California.
 * @license    http://opensource.org/licenses/MIT MIT
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

		/* @FIXME This makes PHP 7.4 have same result as PHP 5.6.
		   However ultimately this may not be what we intended.
		   All session cookie authenticated users get lumped into
		   the same RateLimit bucket for application_id=0, user_id=0.
		*/

		if (!isset($token))
		{
			$token['application_id'] = 0;
			$token['uidNumber'] = 0;
		}

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
		$response->headers->set('X-RateLimit-Limit', $limit);
		$response->headers->set('X-RateLimit-Remaining', $remaining);
		$response->headers->set('X-RateLimit-Reset', $reset);

		// Return response
		return $response;
	}
}
